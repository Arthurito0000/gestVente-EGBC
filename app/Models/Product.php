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
        'quantite',
        'seuil_stock'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'quantite' => 'integer',
        'seuil_stock' => 'integer'
    ];

    // Scopes
    public function scopeEnStock($query)
    {
        return $query->where('quantite', '>', 0);
    }

    public function scopeRuptureStock($query)
    {
        return $query->whereRaw('quantite <= seuil_stock');
    }

    // Accessors
    public function getPrixAchatFormatteAttribute()
    {
        return number_format($this->prix_achat, 2, ',', ' ') . ' €';
    }
}
