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
        // Keep trying to find a unique number
        $attempt = 1;
        do {
            $number = str_pad($attempt, 3, '0', STR_PAD_LEFT);
            $numeroDevis = "DEV-{$number}";

            // Check if this number already exists
            $exists = self::where('numero_devis', $numeroDevis)->exists();

            if (!$exists) {
                return $numeroDevis;
            }

            $attempt++;

            // Safety check to prevent infinite loop
            if ($attempt > 9999) {
                throw new \Exception('Unable to generate unique quote number');
            }
        } while ($exists);
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
