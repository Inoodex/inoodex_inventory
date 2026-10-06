<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'parent_id',
        'level',
        'bank_detail_id',
        'is_active',
        'is_system',
        'opening_balance',
        'current_balance',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'level' => 'integer',
    ];

    // Parent account
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    // Child accounts (sub-accounts)
    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id')->orderBy('account_code');
    }

    // All recursive descendants
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // Linked bank detail profile
    public function bankDetail()
    {
        return $this->belongsTo(BankDetail::class, 'bank_detail_id');
    }

    // Linked journal entry items
    public function journalItems()
    {
        return $this->hasMany(JournalEntryItem::class, 'account_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Determines whether this account type normally increases with Debit.
     * Assets and Expenses have Normal Balance = Debit.
     * Liabilities, Equity, and Revenue have Normal Balance = Credit.
     */
    public function isDebitNormal(): bool
    {
        return in_array($this->account_type, ['asset', 'expense']);
    }

    /**
     * Calculate running balance up to an optional date.
     */
    public function calculateBalance(?string $asOfDate = null): float
    {
        $query = $this->journalItems()
            ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                $q->whereIn('status', ['posted', 'approved']);
                if ($asOfDate) {
                    $q->where('entry_date', '<=', $asOfDate);
                }
            });

        $totalDebit = (float) (clone $query)->sum('debit');
        $totalCredit = (float) (clone $query)->sum('credit');

        $opening = (float) $this->opening_balance;

        if ($this->isDebitNormal()) {
            return $opening + ($totalDebit - $totalCredit);
        } else {
            return $opening + ($totalCredit - $totalDebit);
        }
    }

    /**
     * Batch calculate running balances for multiple accounts in a single SQL query.
     * Returns an associative array: [ account_id => (float) balance ]
     */
    public static function getBatchBalances(?string $asOfDate = null, $accounts = null): array
    {
        $accountsCollection = $accounts instanceof \Illuminate\Support\Collection 
            ? $accounts 
            : ($accounts === null ? self::active()->get() : self::whereIn('id', (array)$accounts)->get());

        $query = \Illuminate\Support\Facades\DB::table('journal_entry_items')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
            ->whereIn('journal_entries.status', ['posted', 'approved']);

        if ($asOfDate) {
            $query->where('journal_entries.entry_date', '<=', $asOfDate);
        }

        $totals = $query->selectRaw('journal_entry_items.account_id, SUM(journal_entry_items.debit) as total_debit, SUM(journal_entry_items.credit) as total_credit')
            ->groupBy('journal_entry_items.account_id')
            ->get()
            ->keyBy('account_id');

        $balances = [];
        foreach ($accountsCollection as $acc) {
            $row = $totals->get($acc->id);
            $totalDebit = $row ? (float)$row->total_debit : 0.00;
            $totalCredit = $row ? (float)$row->total_credit : 0.00;
            $opening = (float)$acc->opening_balance;

            if ($acc->isDebitNormal()) {
                $balances[$acc->id] = $opening + ($totalDebit - $totalCredit);
            } else {
                $balances[$acc->id] = $opening + ($totalCredit - $totalDebit);
            }
        }

        return $balances;
    }
}
