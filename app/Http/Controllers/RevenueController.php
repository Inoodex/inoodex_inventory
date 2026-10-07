<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\DailyExpense;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class RevenueController extends Controller
{
    public function index()
    {
        $revenues = Revenue::orderByDesc('year')->orderByDesc('month')->get();
        return view('frontend.pages.revenue.index', compact('revenues'));
    }

    public function downloadPdf()
    {
        ini_set('memory_limit', '512M');
        $revenues = Revenue::orderByDesc('year')->orderByDesc('month')->get();
        $html = view('pdf.revenue', compact('revenues'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        $fileName = 'Monthly_Revenue_Report_' . now()->format('Y_m_d_His') . '.pdf';
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function generate()
    {
        // 1. Determine earliest activity date across sales, purchases, and expenses
        $dates = [];

        $earliestSale = Sale::min('created_at');
        if ($earliestSale) {
            $dates[] = Carbon::parse($earliestSale)->startOfMonth();
        }

        $earliestPurchase = Purchase::min('created_at');
        if ($earliestPurchase) {
            $dates[] = Carbon::parse($earliestPurchase)->startOfMonth();
        }

        $earliestExpenseDate = DailyExpense::whereNotNull('date')->min('date');
        if ($earliestExpenseDate) {
            $dates[] = Carbon::parse($earliestExpenseDate)->startOfMonth();
        }

        $earliestExpenseCreated = DailyExpense::min('created_at');
        if ($earliestExpenseCreated) {
            $dates[] = Carbon::parse($earliestExpenseCreated)->startOfMonth();
        }

        // If no transactions exist anywhere, default start to the current month
        $startDate = !empty($dates)
            ? collect($dates)->min()->copy()->startOfMonth()
            : now()->startOfMonth();

        // 2. Determine latest month to sync: at least current month, or later if future records exist
        $maxDates = [now()->startOfMonth()];

        $latestSale = Sale::max('created_at');
        if ($latestSale) {
            $maxDates[] = Carbon::parse($latestSale)->startOfMonth();
        }

        $latestPurchase = Purchase::max('created_at');
        if ($latestPurchase) {
            $maxDates[] = Carbon::parse($latestPurchase)->startOfMonth();
        }

        $latestExpenseDate = DailyExpense::whereNotNull('date')->max('date');
        if ($latestExpenseDate) {
            $maxDates[] = Carbon::parse($latestExpenseDate)->startOfMonth();
        }

        $latestExpenseCreated = DailyExpense::max('created_at');
        if ($latestExpenseCreated) {
            $maxDates[] = Carbon::parse($latestExpenseCreated)->startOfMonth();
        }

        $endDate = collect($maxDates)->max()->copy()->startOfMonth();

        if ($startDate->gt($endDate)) {
            $startDate = $endDate->copy();
        }

        // 3. Iterate through every month from start to end and update/create revenue records
        $cursor = $startDate->copy();
        $syncedMonths = 0;
        $activeKeys = [];

        while ($cursor->lte($endDate)) {
            $year = (int) $cursor->year;
            $month = (int) $cursor->month;
            $activeKeys[] = "{$year}-{$month}";

            $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

            $totalSales = (float) Sale::whereBetween('created_at', [$monthStart, $monthEnd])->sum('payble');
            $totalPurchases = (float) Purchase::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_price');
            $totalExpenses = (float) DailyExpense::where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                  ->orWhere(function ($sub) use ($monthStart, $monthEnd) {
                      $sub->whereNull('date')
                          ->whereBetween('created_at', [$monthStart, $monthEnd]);
                  });
            })->sum('amount');

            $netProfit = $totalSales - ($totalPurchases + $totalExpenses);

            Revenue::updateOrCreate(
                ['year' => $year, 'month' => $month],
                [
                    'total_sales' => $totalSales,
                    'total_purchases' => $totalPurchases,
                    'total_expenses' => $totalExpenses,
                    'net_profit' => $netProfit,
                ]
            );

            $syncedMonths++;
            $cursor->addMonth();
        }

        // Clean up any empty/zero records outside the active date range
        Revenue::all()->each(function ($rev) use ($activeKeys) {
            $key = "{$rev->year}-{$rev->month}";
            if (!in_array($key, $activeKeys) && (float)$rev->total_sales == 0 && (float)$rev->total_purchases == 0 && (float)$rev->total_expenses == 0) {
                $rev->delete();
            }
        });

        $startLabel = $startDate->format('M Y');
        $endLabel = $endDate->format('M Y');
        $message = $syncedMonths === 1
            ? "Revenue summary updated successfully for {$startLabel}!"
            : "Revenue summary updated successfully for {$syncedMonths} months ({$startLabel} to {$endLabel})!";

        return redirect()->route('revenues.index')
            ->with('success', $message);
    }

     public function export($id)
    {
        $revenue = Revenue::findOrFail($id);
        ini_set('memory_limit', '512M');
        $html = view('frontend.pages.revenue.pdf', compact('revenue'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        $filename = "Revenue_Report_{$revenue->month_name}_{$revenue->year}.pdf";
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}