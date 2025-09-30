<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'quantite',
        'prix_unitaire',
        'total',
        'date_vente',
        'numero_facture',
        'notes',
    ];

    protected $casts = [
        'date_vente' => 'date',
        'prix_unitaire' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes pour les filtres de période
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_vente', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date_vente', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date_vente', Carbon::now()->month)
                    ->whereYear('date_vente', Carbon::now()->year);
    }

    public function scopeThisQuarter($query)
    {
        return $query->whereBetween('date_vente', [
            Carbon::now()->startOfQuarter(),
            Carbon::now()->endOfQuarter()
        ]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('date_vente', Carbon::now()->year);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_vente', [$startDate, $endDate]);
    }
}
