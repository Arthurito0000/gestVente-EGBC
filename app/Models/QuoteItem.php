<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'product_id',
        'designation',
        'quantite',
        'prix_unitaire',
        'prix_total',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'prix_total' => 'decimal:2',
    ];

    // Relations
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helpers
    public function getPrixUnitaireFormatte()
    {
        return number_format($this->prix_unitaire, 0, ',', ' ') . ' Fcfa';
    }

    public function getPrixTotalFormatte()
    {
        return number_format($this->prix_total, 0, ',', ' ') . ' Fcfa';
    }

    public function getTypeBadge()
    {
        return $this->type === 'materiel' 
            ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Matériel</span>'
            : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Main d\'œuvre</span>';
    }
}
