<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\DailyExpense;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\SalesItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Employee')) {
            // For Employees – show limited data
            return view('frontend.pages.dashboard_employee', [
                'title' => 'Employee Dashboard',
                'data' => [], // empty or limited
            ]);
        }

        $currentYear = date('Y');
        $currentYearInt = (int)$currentYear;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Batch aggregate monthly sales (1 query instead of 12)
        $salesByMonth = Sale::whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(payble) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyRev = [];
        foreach ($months as $key => $monthName) {
            $monthlyRev[$monthName] = (float)($salesByMonth[$key + 1] ?? 0);
        }

        // Batch aggregate yearly sales (1 query instead of 10)
        $minYear = $currentYearInt - 9;
        $salesByYear = Sale::whereYear('created_at', '>=', $minYear)
            ->whereYear('created_at', '<=', $currentYearInt)
            ->selectRaw('YEAR(created_at) as year, SUM(payble) as total')
            ->groupBy('year')
            ->pluck('total', 'year')
            ->toArray();

        $yearlyRev = [];
        for ($i = 0; $i < 10; $i++) {
            $yr = $currentYearInt - $i;
            $yearlyRev[$yr] = (float)($salesByYear[$yr] ?? 0);
        }

        // Project Status Breakdown (1 query instead of 4)
        $statusCounts = Project::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $projectStatusCounts = [
            'In Progress' => (int)($statusCounts['in_progress'] ?? 0),
            'Completed'   => (int)($statusCounts['completed'] ?? 0),
            'Pending'     => (int)($statusCounts['pending'] ?? 0),
            'Cancelled'   => (int)($statusCounts['cancelled'] ?? 0),
        ];

        // Top Projects Budget vs Costs
        $topProjects = Project::with('costs')->latest()->take(5)->get();
        $projectChartNames = [];
        $projectChartBudgets = [];
        $projectChartCosts = [];

        foreach ($topProjects as $p) {
            $projectChartNames[] = \Illuminate\Support\Str::limit($p->project_name, 15);
            $projectChartBudgets[] = (float)$p->budget;
            $projectChartCosts[] = (float)$p->costs->sum('amount');
        }

        // Accounting & Financial Balances
        $today = date('Y-m-d');
        $liquidCash = getAccountBalance('1110', $today);
        $receivables = getAccountBalance('1130', $today);
        $inventoryValuation = getAccountBalance('1140', $today);
        $payables = getAccountBalance('2110', $today);

        $bankAccountParent = \App\Models\ChartOfAccount::where('account_code', '1120')->first();
        $bankBalance = 0.00;
        $bankAccounts = collect();
        if ($bankAccountParent) {
            $bankAccounts = \App\Models\ChartOfAccount::where('parent_id', $bankAccountParent->id)->get();
            if ($bankAccounts->count() > 0) {
                $balances = \App\Models\ChartOfAccount::getBatchBalances($today, $bankAccounts);
                foreach ($bankAccounts as $b) {
                    $b->balance = (float)($balances[$b->id] ?? 0.0);
                    $bankBalance += $b->balance;
                }
            } else {
                $bankBalance = (float)$bankAccountParent->calculateBalance($today);
            }
        }

        $recentJournalEntries = \App\Models\JournalEntry::with('creator')->latest('entry_date')->latest('id')->take(5)->get();

        // Batch aggregate monthly purchases (1 query instead of 12)
        $purchasesByMonth = Purchase::whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Batch aggregate monthly daily expenses (1 query instead of 12)
        $expensesByMonth = DailyExpense::whereYear('date', $currentYear)
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Batch aggregate monthly projects (1 query instead of 12)
        $projectsByMonth = Project::whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyPurch = [];
        $monthlyExp = [];
        $monthlyProj = [];
        foreach ($months as $key => $monthName) {
            $mNum = $key + 1;
            $monthlyPurch[$monthName] = (float)($purchasesByMonth[$mNum] ?? 0);
            $monthlyExp[$monthName] = (float)($expensesByMonth[$mNum] ?? 0);
            $monthlyProj[$monthName] = (int)($projectsByMonth[$mNum] ?? 0);
        }

        // Month-over-month growth percentages
        $currentMonthIdx = now()->month - 1;
        $prevMonthIdx = $currentMonthIdx - 1;

        if ($prevMonthIdx < 0) {
            $prevMonthSales = Sale::whereYear('created_at', $currentYear - 1)->whereMonth('created_at', 12)->sum('payble');
            $prevMonthPurchase = Purchase::whereYear('created_at', $currentYear - 1)->whereMonth('created_at', 12)->sum('total_price');
            $prevMonthExpense = DailyExpense::whereYear('date', $currentYear - 1)->whereMonth('date', 12)->sum('amount');
        } else {
            $prevMonthSales = $monthlyRev[$months[$prevMonthIdx]];
            $prevMonthPurchase = $monthlyPurch[$months[$prevMonthIdx]];
            $prevMonthExpense = $monthlyExp[$months[$prevMonthIdx]];
        }

        $growthPct = function ($current, $previous) {
            if ((float) $previous <= 0) {
                return 0;
            }
            return (int) round((($current - $previous) / $previous) * 100);
        };

        $salesGrowthPct = $growthPct($monthlyRev[$months[$currentMonthIdx]], $prevMonthSales);
        $purchaseGrowthPct = $growthPct($monthlyPurch[$months[$currentMonthIdx]], $prevMonthPurchase);
        $expenseGrowthPct = $growthPct($monthlyExp[$months[$currentMonthIdx]], $prevMonthExpense);

        // Dashboard list / metric data
        $totalCustomers = Customer::count();

        $lowStockProducts = Product::with(['inventory', 'category', 'brand'])
            ->where(function ($q) {
                $q->whereHas('inventory', function ($qi) {
                    $qi->whereRaw('inventories.current_stock <= COALESCE(products.min_stock_alert, 5)');
                })->orWhereDoesntHave('inventory');
            })
            ->where('status', '1')
            ->take(10)
            ->get();

        $topProducts = SalesItem::selectRaw('product_id, SUM(qty) as total_qty, SUM(total_price) as total_revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'product_name'  => $item->product->name ?? 'N/A',
                    'total_qty'     => $item->total_qty,
                    'total_revenue' => $item->total_revenue,
                ];
            });

        $topCustomers = Sale::selectRaw('customer_id, COUNT(*) as total_sales, SUM(payble) as total_amount')
            ->whereNotNull('customer_id')
            ->with('customer')
            ->groupBy('customer_id')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get()
            ->map(function ($sale) {
                return (object) [
                    'name'  => $sale->customer->name ?? 'Walk-in Customer',
                    'phone' => $sale->customer->phone ?? null,
                ];
            });

        $expenseBreakdown = DailyExpense::join('expense_categories', 'expense_categories.id', '=', 'daily_expenses.expense_category_id')
            ->selectRaw('expense_categories.name as category_name, SUM(daily_expenses.amount) as total')
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $newCustomersCount = Customer::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $newCustomerPct = $totalCustomers > 0 ? (int) round(($newCustomersCount / $totalCustomers) * 100) : 0;
        $returningCustomersCount = $totalCustomers - $newCustomersCount;
        $returningCustomerPct = 100 - $newCustomerPct;

        $stats = [
            'todaysSalesRevenue'     => Sale::whereDate('created_at', Carbon::today())->sum('payble'),
            'thisWeeksSalesRevenue'  => Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('payble'),
            'thisMonthsSalesRevenue' => Sale::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('payble'),
            'thisYearsSalesRevenue'  => Sale::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('payble'),

            'todaysPurchaseRevenue'    => Purchase::whereDate('created_at', Carbon::today())->sum('total_price'),
            'thisWeeksPurchaseRevenue' => Purchase::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price'),
            'thisMonthsPurchaseRevenue' => Purchase::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_price'),
            'thisYearsPurchaseRevenue' => Purchase::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('total_price'),

            'todaysExpense'     => DailyExpense::whereDate('date', Carbon::today())->sum('amount'),
            'thisWeeksExpense'  => DailyExpense::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount'),
            'thisMonthsExpense' => DailyExpense::whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('amount'),
            'thisYearsExpense'  => DailyExpense::whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('amount'),

            'totalCustomers'    => $totalCustomers,
            'totalProjects'     => Project::count(),
            'totalEmployees'    => Employee::count(),
            'totalProducts'     => Product::count(),
            'totalVendors'      => Vendor::count(),
            'totalSalesCount'   => Sale::count(),
            'totalCategories'   => Category::count(),
            'todayOrdersCount'  => Sale::whereDate('created_at', Carbon::today())->count(),
            'totalInvoiceDue'   => Sale::sum('due_payment'),
            'totalSalesReturnAmount' => \App\Models\ProductReturn::sum('total_refund_amount'),

            'salesGrowthPct'    => $salesGrowthPct,
            'purchaseGrowthPct' => $purchaseGrowthPct,
            'expenseGrowthPct'  => $expenseGrowthPct,

            'lowStockProducts'  => $lowStockProducts,
            'topProducts'       => $topProducts,
            'topCustomers'      => $topCustomers,
            'recentTransactions' => Sale::with('customer')->latest()->take(10)->get(),
            'expenseBreakdown'  => $expenseBreakdown,

            'newCustomersCount'      => $newCustomersCount,
            'returningCustomersCount' => $returningCustomersCount,
            'newCustomerPct'         => $newCustomerPct,
            'returningCustomerPct'   => $returningCustomerPct,

            'recentSales'       => Sale::latest()->take(5)->get(),
            'recentProjects'    => Project::with('client')->latest()->take(5)->get(),

            'monthlyRevenue'        => $monthlyRev,
            'monthlyPurchase'       => $monthlyPurch,
            'monthlyExpense'        => $monthlyExp,
            'monthlyProjects'       => $monthlyProj,
            'yearlyRevenue'         => $yearlyRev,
            'projectStatusCounts'   => $projectStatusCounts,
            'projectChartNames'     => $projectChartNames,
            'projectChartBudgets'   => $projectChartBudgets,
            'projectChartCosts'     => $projectChartCosts,

            // Financial Accounting Metrics
            'liquidCash'            => $liquidCash,
            'bankBalance'           => $bankBalance,
            'receivables'           => $receivables,
            'payables'              => $payables,
            'inventoryValuation'    => $inventoryValuation,
            'bankAccounts'          => $bankAccounts,
            'recentJournalEntries'  => $recentJournalEntries,
        ];

        return view('frontend.pages.index', $stats);
    }

}
