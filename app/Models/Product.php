<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'nom',
        'prix_achat',
        'categorie',
        'seuil_stock',
        'prix_vente'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'seuil_stock' => 'integer'
    ];

    // Scopes
    public function scopeEnStock($query)
    {
        return $query->whereHas('stock', function($q) {
            $q->where('quantite', '>', 0);
        });
    }

    public function scopeEnRupture($query)
    {
        return $query->whereHas('stock', function($q) {
            $q->whereColumn('quantite', '<=', 'seuil');
        });
    }
    
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
    
    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
    
    // Accessors
    public function getPrixAchatFormatteAttribute()
    {
        return number_format((float) $this->prix_achat, 2, ',', ' ') . ' Fcfa';
    }
    
    public function getPrixVenteFormatteAttribute()
    {
        return number_format((float) $this->prix_vente, 2, ',', ' ') . ' Fcfa';
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_product')
                    ->withPivot('quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
    }
}
