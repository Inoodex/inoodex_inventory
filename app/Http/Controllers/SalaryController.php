<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use App\Models\TaDa;
use App\Models\ExpenseCategory;
use App\Models\DailyExpense;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::with('employee')->latest()->get();
        return view('frontend.pages.salary.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        $currentMonth = date('Y-m');
        $taDaData = [];
        return view('frontend.pages.salary.create', compact('employees', 'currentMonth', 'taDaData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required',
            'basic_salary' => 'required|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:500',
        ]);

        $basic = (float) $request->basic_salary;
        $allowance = (float) ($request->allowance ?? 0);
        $deduction = (float) ($request->deduction ?? 0);
        $advance = (float) ($request->advance ?? 0);
        $netSalary = max(0, $basic + $allowance - $deduction - $advance);

        DB::transaction(function () use ($request, $netSalary) {
            $data = $request->all();
            $data['user_id'] = Auth::id() ?? 1;
            $data['date'] = $request->payment_date ?: date('Y-m-d');
            $data['amount'] = $netSalary;
            $data['status'] = $request->payment_status === 'paid' ? '1' : '0';
            $data['net_salary'] = $netSalary;
            $salary = Salary::create($data);

            // Auto-post double-entry payroll voucher if marked as paid
            if ($salary->payment_status === 'paid') {
                $this->postSalaryJournal($salary->fresh('employee'));
            }
        });

        return redirect()->route('salary.index')->with('success', 'Salary record created and processed successfully.');
    }

    public function edit($id)
    {
        $salary = Salary::findOrFail($id);
        $employees = Employee::orderBy('name')->get();
        return view('frontend.pages.salary.edit', compact('salary','employees'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required',
            'basic_salary' => 'required|numeric|min:0',
            'advance' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid',
            'payment_date' => 'nullable|date',
            'note' => 'nullable|string|max:500',
        ]);

        $basic = (float) $request->basic_salary;
        $allowance = (float) ($request->allowance ?? 0);
        $deduction = (float) ($request->deduction ?? 0);
        $advance = (float) ($request->advance ?? 0);
        $netSalary = max(0, $basic + $allowance - $deduction - $advance);

        $salary = Salary::findOrFail($id);

        DB::transaction(function () use ($salary, $request, $netSalary) {
            // Reverse any prior payroll voucher
            $this->reverseSalaryJournal($salary->id);

            $data = $request->all();
            $data['amount'] = $netSalary;
            $data['date'] = $request->payment_date ?: ($salary->date ?? date('Y-m-d'));
            $data['status'] = $request->payment_status === 'paid' ? '1' : '0';
            $data['net_salary'] = $netSalary;
            $salary->update($data);

            // Post new journal entry if status is paid
            if ($salary->payment_status === 'paid') {
                $this->postSalaryJournal($salary->fresh('employee'));
            }
        });

        return redirect()->route('salary.index')->with('success', 'Salary record updated and ledger adjusted successfully.');
    }

    public function destroy($id)
    {
        $salary = Salary::findOrFail($id);

        DB::transaction(function () use ($salary) {
            $this->reverseSalaryJournal($salary->id);
            $salary->delete();
        });

        return redirect()->route('salary.index')->with('success', 'Salary record deleted and journal entry reversed successfully.');
    }

    /**
     * Post double-entry payroll voucher for a Salary disbursement.
     */
    protected function postSalaryJournal(Salary $salary): void
    {
        try {
            if ($salary->payment_status !== 'paid') {
                return;
            }

            $basic = (float) $salary->basic_salary;
            $allowance = (float) ($salary->allowance ?? 0);
            $gross = $basic + $allowance;
            $advance = (float) ($salary->advance ?? 0);
            $deduction = (float) ($salary->deduction ?? 0);
            $net = (float) $salary->net_salary;

            if ($gross <= 0 && $net <= 0) {
                return;
            }

            $salaryAcc = ChartOfAccount::where('account_code', '5210')->first(); // Salaries & Staff Expenses
            $cashAcc = ChartOfAccount::where('account_code', '1110')->first();   // Cash in Hand
            $bankAcc = ChartOfAccount::where('account_code', '1120')->first();   // Bank & Mobile Accounts
            $arAcc = ChartOfAccount::where('account_code', '1130')->first();     // Advance / Staff Receivables

            if (!$salaryAcc) {
                return;
            }

            $employee = $salary->employee;
            $empName = $employee ? $employee->name : ('Employee #' . $salary->employee_id);
            $month = $salary->month ?? date('Y-m');

            $items = [];

            // Debit: Salary & Staff Expense (Gross Amount)
            $items[] = [
                'account_id'  => $salaryAcc->id,
                'debit'       => $gross,
                'credit'      => 0.00,
                'description' => "Gross salary expense for {$empName} [Month: {$month}]",
            ];

            // Credit: Advance deduction (if any)
            if ($advance > 0) {
                $advCreditAcc = $arAcc ?: $salaryAcc;
                $items[] = [
                    'account_id'  => $advCreditAcc->id,
                    'debit'       => 0.00,
                    'credit'      => $advance,
                    'description' => "Advance salary adjusted/recovered for {$empName} [Month: {$month}]",
                ];
            }

            // Credit: Other deductions (if any)
            if ($deduction > 0) {
                $dedCreditAcc = $salaryAcc;
                $items[] = [
                    'account_id'  => $dedCreditAcc->id,
                    'debit'       => 0.00,
                    'credit'      => $deduction,
                    'description' => "Payroll deduction for {$empName} [Month: {$month}]",
                ];
            }

            // Credit: Net Cash/Bank Payout
            $paymentAcc = $cashAcc ?: $salaryAcc;
            if ($net > 0) {
                $items[] = [
                    'account_id'  => $paymentAcc->id,
                    'debit'       => 0.00,
                    'credit'      => $net,
                    'description' => "Net salary disbursed to {$empName} via {$paymentAcc->account_name} [Month: {$month}]",
                ];
            }

            // Verify double-entry balancing equilibrium
            $totalDebit = array_sum(array_column($items, 'debit'));
            $totalCredit = array_sum(array_column($items, 'credit'));

            if (abs($totalDebit - $totalCredit) > 0.001) {
                $diff = $totalDebit - $totalCredit;
                $items[] = [
                    'account_id'  => $paymentAcc->id,
                    'debit'       => 0.00,
                    'credit'      => $diff,
                    'description' => "Balancing adjustment for {$empName}",
                ];
            }

            postJournalEntry([
                'entry_date'     => $salary->payment_date ? date('Y-m-d', strtotime($salary->payment_date)) : date('Y-m-d'),
                'reference_type' => 'salary',
                'reference_id'   => $salary->id,
                'description'    => "Salary Disbursed for {$empName} — Month: {$month} (Net: ৳" . number_format($net, 2) . ")" . ($salary->note ? " [Note: {$salary->note}]" : ''),
                'status'         => 'approved',
                'created_by'     => Auth::id() ?? 1,
                'items'          => $items,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Salary auto-journal posting failed: ' . $e->getMessage(), [
                'salary_id' => $salary->id,
            ]);
        }
    }

    /**
     * Reverse any existing journal entry for a Salary record.
     */
    protected function reverseSalaryJournal(int $salaryId): void
    {
        try {
            $existing = JournalEntry::where('reference_type', 'salary')
                ->where('reference_id', $salaryId)
                ->whereIn('status', ['posted', 'approved'])
                ->latest()
                ->first();

            if ($existing) {
                reverseJournalEntry($existing->id, "Salary Record #{$salaryId} updated or deleted");
            }
        } catch (\Throwable $e) {
            Log::warning('Salary auto-journal reversal failed: ' . $e->getMessage());
        }
    }

public function getTaDaDataAjax(Request $request)
{
    $employeeId = $request->employee_id;
    $month = $request->month;
    $year = substr($month, 0, 4);
    $monthNum = substr($month, 5, 2);
    
    $taDaRecords = TaDa::where('employee_id', $employeeId)
                      ->whereYear('date', $year)
                      ->whereMonth('date', $monthNum)
                      ->get();

    $totalAdvance = 0;
    $totalClaim = 0;

    foreach ($taDaRecords as $record) {
        if ($record->payment_type === 'Advance') {
            $totalAdvance += $record->remaining_amount;
        } elseif ($record->payment_type === 'Claim') {
            $totalClaim += $record->amount;
        }
    }

    return response()->json([
        'total_advance' => $totalAdvance,
        'total_claim' => $totalClaim,
        'records_count' => $taDaRecords->count()
    ]);
}
// public function getAdvanceSumByMonth($id, Request $request)
// {
//     // Find the "Advance Salary" category dynamically
//     $advanceCategory = ExpenseCategory::where('name', 'Advance Salary')->first();
    
//     if (!$advanceCategory) {
//         return response()->json(['sum' => 0]);
//     }

//     $query = DailyExpense::where('employee_id', $id)
//                          ->where('expense_category_id', $advanceCategory->id);
    
//     // Filter by month if provided
//     if ($request->has('month') && $request->month) {
//         $year = substr($request->month, 0, 4);
//         $monthNum = substr($request->month, 5, 2);
//         $query->whereYear('date', $year)
//               ->whereMonth('date', $monthNum);
//     }
    
//     $advance = $query->sum('amount');
    
//     return response()->json(['sum' => $advance]);
// }
}