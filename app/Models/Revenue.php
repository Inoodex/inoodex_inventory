<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'total_sales',
        'total_purchases',
        'total_expenses',
        'net_profit',
        'remarks',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    public function getMonthNameAttribute()
    {
        return date("F", mktime(0, 0, 0, $this->month, 10));
    }

    public function getNetProfitAttribute($value)
    {
        if ($value !== null) {
            return (float) $value;
        }
        return (float) ($this->total_sales - ($this->total_purchases + $this->total_expenses));
    }

    public function getFormattedProfitAttribute()
    {
        return number_format($this->net_profit, 2);
    }
}