<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const FOR_PURCHASE = 1;
    public const FOR_SERVICE  = 1;
    public const FOR_SALE     = 2;
    public const FOR_PROJECT  = 3;
    public const FOR_REFUND   = 4;

    protected $fillable = [
        'customer_id',
        'sale_id',
        'payment_for',
        'payment_method',
        'amount',
        'remarks',
        'status',
        'created_by',
        'updated_by'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
