<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Tests\TestCase;

class AccountingCoreTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            PermissionMiddleware::class,
            RoleMiddleware::class,
            RoleOrPermissionMiddleware::class,
        ]);
        $this->admin = User::factory()->create();
    }

    public function test_batch_balances_matches_individual_calculate_balance(): void
    {
        // 1. Create an asset account (Debit normal)
        $cash = ChartOfAccount::create([
            'account_code'    => '1110',
            'account_name'    => 'Cash in Hand',
            'account_type'    => 'asset',
            'opening_balance' => 1000.00,
            'is_active'       => true,
            'level'           => 1,
        ]);

        // 2. Create a liability account (Credit normal)
        $payable = ChartOfAccount::create([
            'account_code'    => '2110',
            'account_name'    => 'Accounts Payable',
            'account_type'    => 'liability',
            'opening_balance' => 500.00,
            'is_active'       => true,
            'level'           => 1,
        ]);

        // 3. Create a posted journal entry: Debit Cash 200, Credit Payable 200
        $journal = JournalEntry::create([
            'journal_no'   => 'JV-001',
            'entry_date'   => '2026-01-15',
            'status'       => 'posted',
            'total_debit'  => 200.00,
            'total_credit' => 200.00,
            'created_by'   => $this->admin->id,
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $journal->id,
            'account_id'       => $cash->id,
            'debit'            => 200.00,
            'credit'           => 0.00,
            'description'      => 'Cash deposit',
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $journal->id,
            'account_id'       => $payable->id,
            'debit'            => 0.00,
            'credit'           => 200.00,
            'description'      => 'Vendor bill credited',
        ]);

        // Calculate balances individually
        $individualCashBalance = $cash->calculateBalance('2026-01-31');
        $individualPayableBalance = $payable->calculateBalance('2026-01-31');

        // Calculate in batch
        $batchBalances = ChartOfAccount::getBatchBalances('2026-01-31', collect([$cash, $payable]));

        // Expected: Cash (Asset) = opening(1000) + debits(200) - credits(0) = 1200
        $this->assertEquals(1200.00, $individualCashBalance);
        $this->assertEquals(1200.00, $batchBalances[$cash->id]);

        // Expected: Payable (Liability) = opening(500) + credits(200) - debits(0) = 700
        $this->assertEquals(700.00, $individualPayableBalance);
        $this->assertEquals(700.00, $batchBalances[$payable->id]);
    }

    public function test_payment_controller_prevents_overpayment(): void
    {
        $this->actingAs($this->admin);

        $customer = Customer::create([
            'name'  => 'Test Client',
            'phone' => '01700000000',
        ]);

        $sale = Sale::create([
            'order_no'         => 'ORD-999',
            'customer_id'      => $customer->id,
            'product_id'       => 1,
            'qty'              => 1,
            'sale_type'        => 'pos',
            'sub_total'        => 500,
            'total'            => 500,
            'bill'             => 500,
            'payble'           => 500,
            'advanced_payment' => 0,
            'due_payment'      => 500,
            'status'           => 'credit',
        ]);

        // Attempt to pay 600 on a 500 due bill
        $response = $this->from(route('sales.index'))->post(route('add.payment'), [
            'id'                => $sale->id,
            'payment_for'       => '2', // Sale
            'amount'            => 600,
            'payment_method_id' => 'cash',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('payments', [
            'sale_id' => $sale->id,
            'amount'  => 600,
        ]);

        // Verify sale was not updated
        $sale->refresh();
        $this->assertEquals(500, (float)$sale->due_payment);
    }

    public function test_payment_controller_records_payment_and_updates_due_atomically(): void
    {
        $this->actingAs($this->admin);

        $customer = Customer::create([
            'name'  => 'Test Client 2',
            'phone' => '01711111111',
        ]);

        $sale = Sale::create([
            'order_no'         => 'ORD-1000',
            'customer_id'      => $customer->id,
            'product_id'       => 1,
            'qty'              => 1,
            'sale_type'        => 'pos',
            'sub_total'        => 500,
            'total'            => 500,
            'bill'             => 500,
            'payble'           => 500,
            'advanced_payment' => 100,
            'due_payment'      => 400,
            'status'           => 'partial',
        ]);

        // Make valid partial payment of 250
        $response = $this->from(route('sales.index'))->post(route('add.payment'), [
            'id'                => $sale->id,
            'payment_for'       => '2', // Sale
            'amount'            => 250,
            'payment_method_id' => 'cash',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'sale_id'        => $sale->id,
            'payment_for'    => 2,
            'amount'         => 250,
            'payment_method' => 'cash',
        ]);

        $sale->refresh();
        // Paid: 100 + 250 = 350; Due: 500 - 350 = 150
        $this->assertEquals(350, (float)$sale->advanced_payment);
        $this->assertEquals(150, (float)$sale->due_payment);
    }
}
