<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'month',
        'date',
        'amount',
        'basic_salary',
        'advance',
        'allowance',
        'deduction',
        'net_salary',
        'payment_status',
        'payment_date',
        'note',
        'status',
        'remarks',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reference_id')->where('reference_type', 'salary');
    }
}