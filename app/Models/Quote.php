<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_devis',
        'user_id',
        'client_nom',
        'objet',
        'total_materiel',
        'main_oeuvre',
        'total_general',
        'date_devis',
    ];

    protected $casts = [
        'date_devis' => 'date',
        'total_materiel' => 'decimal:2',
        'main_oeuvre' => 'decimal:2',
        'total_general' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    // Helpers

    public static function generateNumeroDevis()
    {
        $lastQuote = self::latest()->first();
        
        if ($lastQuote) {
            $lastNumber = (int) str_replace('DEV-', '', $lastQuote->numero_devis);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "DEV-{$newNumber}";
    }

    public function getTotalMaterielFormatte()
    {
        return number_format($this->total_materiel, 0, ',', ' ') . ' Fcfa';
    }

    public function getMainOeuvreFormatte()
    {
        return number_format($this->main_oeuvre, 0, ',', ' ') . ' Fcfa';
    }

    public function getTotalGeneralFormatte()
    {
        return number_format($this->total_general, 0, ',', ' ') . ' Fcfa';
    }
}
