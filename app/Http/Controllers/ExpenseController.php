<?php

namespace App\Http\Controllers;

use App\Models\DailyExpense;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
   

    public function index(Request $request)
    {
        // Start the query on daily_expenses, joining to categories only:
        $query = DailyExpense::leftJoin('expense_categories', 'expense_categories.id', '=', 'daily_expenses.expense_category_id');

        // Date filtering
        $defaultFilter = true;
        if ($request->from && $request->to) {
            $from = date('Y-m-d 00:00:00', strtotime($request->from));
            $to   = date('Y-m-d 23:59:59', strtotime($request->to));
            $query->whereBetween('daily_expenses.created_at', [$from, $to]);
            $defaultFilter = false;
        }

        // Spend method filter
        if ($request->spend_method) {
            $query->where('daily_expenses.spend_method', $request->spend_method);
            $defaultFilter = false;
        }

        // Expense category filter
        if ($request->expense_category_id) {
            $query->where('daily_expenses.expense_category_id', $request->expense_category_id);
            $defaultFilter = false;
        }

        // Remarks search
        if ($request->key) {
            $query->where('daily_expenses.remarks', 'like', '%' . $request->key . '%');
            $defaultFilter = false;
        }

        // Default to current month
        if ($defaultFilter) {
            $startOfMonth = now()->startOfMonth()->startOfDay();
            $endOfMonth   = now()->endOfMonth()->endOfDay();
            $query->whereBetween('daily_expenses.created_at', [$startOfMonth, $endOfMonth]);
        }

        // Select what we need
        $dailyExpense = $query
            ->with('employee')
            ->select(
                'daily_expenses.*',
                'expense_categories.name as category_name'
            )
            ->orderBy('daily_expenses.id', 'desc')
            ->get();

        // Pull only the active categories for the filter dropdown
        $categories = ExpenseCategory::where('status', 1)->orderBy('name')->get();

        // PDF export shortcut
        if ($request->search_for === 'pdf') {
            ini_set('memory_limit', '512M');
            $html = view('pdf.daily_expense', compact('dailyExpense', 'request', 'categories'))->render();
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'Helvetica',
            ]);
            $mpdf->WriteHTML($html);
            $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="daily_expense.pdf"',
            ]);
        }

        // Render index view
        return view('frontend.pages.expense.index', compact('dailyExpense','request','categories'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
            $users = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.name as roleName')
                ->orderBy('users.id', 'desc')
                ->get();

            $employees = Employee::where('status', 'active')->get();

            $categories = ExpenseCategory::all();

            return view('frontend.pages.expense.create', compact('users', 'categories', 'employees'));
    }


    /**
     * Store a newly created expense and auto-post to General Ledger.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'spend_method' => 'required|in:cash,card,bank_transfer',
            'remarks' => 'nullable|string',
            'expense_category_id' => 'required|exists:expense_categories,id',
        ]);

        DB::transaction(function () use ($request) {
            $expense = DailyExpense::create([
                'user_id' => auth()->id(),
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'expense_category_id' => $request->expense_category_id,
                'amount' => $request->amount,
                'spend_method' => $request->spend_method,
                'remarks' => $request->remarks,
            ]);

            // Auto-post double-entry journal voucher to General Ledger
            $this->postExpenseJournal($expense->fresh(['expenseCategory', 'employee']));
        });

        return redirect()->route('dailyExpenses.index')->with('success', 'Daily Expense recorded and posted to General Ledger.');
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = DailyExpense::where('id', $id)->first();        
        $users = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as roleName')
            ->orderBy('users.id', 'desc')
            ->get();
        $employees = Employee::where('status', 'active')->get();
        $categories = ExpenseCategory::where('status', 1)->orderBy('name')->get();
        return view('frontend.pages.expense.edit', compact('users', 'expense', 'categories', 'employees'));

    }

    /**
     * Update the specified expense and adjust ledger entries.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date'                => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'spend_method'        => 'required|in:cash,card,bank_transfer',
            'remarks'             => 'nullable|string|max:1000',
            'employee_id'         => 'nullable|exists:employees,id',
        ]);

        $expense = DailyExpense::findOrFail($id);

        DB::transaction(function () use ($expense, $request) {
            // Reverse prior voucher if any
            $this->reverseExpenseJournal($expense->id);

            // Update expense record
            $expense->update([
                'date'                => $request->date,
                'expense_category_id' => $request->expense_category_id,
                'employee_id'         => $request->employee_id,
                'amount'              => $request->amount,
                'spend_method'        => $request->spend_method,
                'remarks'             => $request->remarks,
            ]);

            // Post new updated journal entry
            $this->postExpenseJournal($expense->fresh(['expenseCategory', 'employee']));
        });

        return redirect()->route('dailyExpenses.index')->with('success', 'Daily Expense updated and ledger adjusted successfully.');
    }

    /**
     * Remove the specified expense and reverse its journal voucher.
     */
    public function destroy(string $id)
    {
        $expense = DailyExpense::findOrFail($id);

        DB::transaction(function () use ($expense) {
            $this->reverseExpenseJournal($expense->id);
            $expense->delete();
        });

        return redirect()->back()->with(['success' => 'Expense deleted and associated journal entry reversed successfully.']);
    }

    /**
     * Post or update double-entry journal voucher for a DailyExpense record.
     */
    protected function postExpenseJournal(DailyExpense $expense): void
    {
        try {
            $amount = (float) $expense->amount;
            if ($amount <= 0) {
                return;
            }

            // Determine Debit Account (Expense Account)
            $categoryName = $expense->expenseCategory?->name ?? 'Office Expense';
            $expenseAcc = null;
            if (stripos($categoryName, 'salary') !== false || stripos($categoryName, 'staff') !== false) {
                $expenseAcc = ChartOfAccount::where('account_code', '5210')->first();
            }
            if (!$expenseAcc) {
                $expenseAcc = ChartOfAccount::where('account_code', '5230')->first();
            }

            // Determine Credit Account (Payment Source Asset Account)
            $sourceAcc = null;
            if ($expense->spend_method === 'cash') {
                $sourceAcc = ChartOfAccount::where('account_code', '1110')->first();
            } else {
                $sourceAcc = ChartOfAccount::where('account_code', '1120')->first();
            }
            if (!$sourceAcc) {
                $sourceAcc = ChartOfAccount::where('account_code', '1110')->first();
            }

            if ($expenseAcc && $sourceAcc) {
                $empName = $expense->employee ? " (Staff: {$expense->employee->name})" : '';
                $methodLabel = ucfirst(str_replace('_', ' ', $expense->spend_method ?? 'cash'));
                $desc = "Daily Expense [{$categoryName}] — " . ($expense->remarks ?: 'Operational Expense') . "{$empName} [Paid via {$methodLabel}]";

                postJournalEntry([
                    'entry_date'     => $expense->date ? date('Y-m-d', strtotime($expense->date)) : date('Y-m-d'),
                    'reference_type' => 'expense',
                    'reference_id'   => $expense->id,
                    'description'    => $desc,
                    'status'         => 'approved',
                    'created_by'     => Auth::id() ?? 1,
                    'items'          => [
                        [
                            'account_id'  => $expenseAcc->id,
                            'debit'       => $amount,
                            'credit'      => 0.00,
                            'description' => "Expense recorded under {$expenseAcc->account_name} ({$categoryName})",
                        ],
                        [
                            'account_id'  => $sourceAcc->id,
                            'debit'       => 0.00,
                            'credit'      => $amount,
                            'description' => "Payment disbursed via {$sourceAcc->account_name}",
                        ],
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Expense auto-journal posting failed: ' . $e->getMessage(), [
                'expense_id' => $expense->id,
            ]);
        }
    }

    /**
     * Reverse any existing journal entry for a DailyExpense.
     */
    protected function reverseExpenseJournal(int $expenseId): void
    {
        try {
            $existing = JournalEntry::where('reference_type', 'expense')
                ->where('reference_id', $expenseId)
                ->whereIn('status', ['posted', 'approved'])
                ->latest()
                ->first();

            if ($existing) {
                reverseJournalEntry($existing->id, "Daily Expense #{$expenseId} updated or deleted");
            }
        } catch (\Throwable $e) {
            Log::warning('Expense auto-journal reversal failed: ' . $e->getMessage());
        }
    }

    public function getAdvanceSum($employeeId)
{
    $advanceCategory = ExpenseCategory::where('name', 'Advance Salary')->first();

    if (!$advanceCategory) {
        return response()->json(['sum' => 0]);
    }

    $sum = DailyExpense::where('employee_id', $employeeId)
        ->where('expense_category_id', $advanceCategory->id)
        ->sum('amount');

    return response()->json(['sum' => $sum]);
}

}