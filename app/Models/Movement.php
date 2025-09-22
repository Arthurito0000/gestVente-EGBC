<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;

class Movement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'type',
        'quantite',
        'motif',
        'date'
    ];
    
    protected $casts = [
        'date' => 'datetime'
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
