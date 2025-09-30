<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use Carbon\Carbon;

class Movement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'type',
        'ajustement_type',
        'quantite',
        'prix_achat',
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

    /**
     * Scopes pour les filtres de période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
    }
}
