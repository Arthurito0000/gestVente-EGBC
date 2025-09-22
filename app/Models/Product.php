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
        'seuil_stock'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
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
        return number_format($this->prix_achat, 2, ',', ' ') . ' €';
    }
}
