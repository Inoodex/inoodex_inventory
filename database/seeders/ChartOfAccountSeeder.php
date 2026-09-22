<?php

namespace Database\Seeders;

use App\Models\BankDetail;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the standard 2-tier Chart of Accounts (14 core accounts) & current Fiscal Year.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Ensure Active Fiscal Year
            $year = date('Y');
            FiscalYear::firstOrCreate(
                ['year_name' => "{$year}-" . ($year + 1)],
                [
                    'start_date' => "{$year}-01-01",
                    'end_date'   => "{$year}-12-31",
                    'is_active'  => true,
                    'is_closed'  => false,
                ]
            );

            // 2. Standard 2-Tier Chart of Accounts (Root -> Sub-accounts)
            $structure = [
                // Assets (1000)
                ['1000', 'Assets', 'asset', [
                    ['1110', 'Cash in Hand', 'asset'],
                    ['1120', 'Bank & Mobile Accounts', 'asset'],
                    ['1130', 'Accounts Receivable (Customer Dues)', 'asset'],
                    ['1140', 'Inventory Asset / Stock', 'asset'],
                    ['1210', 'Office Equipment & Fixed Assets', 'asset'],
                ]],
                // Liabilities (2000)
                ['2000', 'Liabilities', 'liability', [
                    ['2110', 'Accounts Payable (Supplier / Vendor Dues)', 'liability'],
                    ['2120', 'VAT / Tax Payable', 'liability'],
                ]],
                // Equity (3000)
                ['3000', 'Equity', 'equity', [
                    ['3100', 'Owner Capital', 'equity'],
                    ['3200', 'Retained Earnings', 'equity'],
                ]],
                // Revenue (4000)
                ['4000', 'Revenue / Income', 'revenue', [
                    ['4110', 'Sales Revenue', 'revenue'],
                    ['4120', 'Service & Project Revenue', 'revenue'],
                    ['4140', 'Delivery Charge Income', 'revenue'],
                ]],
                // Expenses (5000)
                ['5000', 'Expenses', 'expense', [
                    ['5110', 'Cost of Goods Sold (Purchase Expense)', 'expense'],
                    ['5210', 'Salaries & Staff Expenses', 'expense'],
                    ['5230', 'Daily Office Expenses', 'expense'],
                ]],
            ];

            foreach ($structure as [$code, $name, $type, $children]) {
                $root = ChartOfAccount::updateOrCreate(
                    ['account_code' => $code],
                    [
                        'account_name' => $name,
                        'account_type' => $type,
                        'level'        => 1,
                        'parent_id'    => null,
                        'is_active'    => true,
                        'is_system'    => true,
                    ]
                );

                foreach ($children as [$childCode, $childName, $childType]) {
                    ChartOfAccount::updateOrCreate(
                        ['account_code' => $childCode],
                        [
                            'account_name' => $childName,
                            'account_type' => $childType,
                            'level'        => 2,
                            'parent_id'    => $root->id,
                            'is_active'    => true,
                            'is_system'    => true,
                        ]
                    );
                }
            }

            // 3. Auto-link any existing Bank Details under Bank & Mobile Accounts (1120)
            $bankParent = ChartOfAccount::where('account_code', '1120')->first();
            if ($bankParent) {
                $seq = 1;
                foreach (BankDetail::all() as $bank) {
                    ChartOfAccount::updateOrCreate(
                        ['bank_detail_id' => $bank->id],
                        [
                            'account_code' => '1120-' . str_pad($seq++, 2, '0', STR_PAD_LEFT),
                            'account_name' => "{$bank->bank_name} ({$bank->account_number})",
                            'account_type' => 'asset',
                            'parent_id'    => $bankParent->id,
                            'level'        => 3,
                            'is_active'    => $bank->is_active,
                            'is_system'    => false,
                        ]
                    );
                }
            }
        });
    }
}
