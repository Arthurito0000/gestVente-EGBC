<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'code',
        'invoice_number',
        'client_name',
        'client_phone',
        'vendor_name',
        'client_location',
        'invoice_date',
        'total_amount',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product')
                    ->withPivot('quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
    }
}
