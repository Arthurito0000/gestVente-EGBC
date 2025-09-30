<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'statut',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'statut' => 'string',
        ];
    }

    /**
     * Vérifier si l'utilisateur est un admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('administrateur');
    }

    /**
     * Vérifier si l'utilisateur est un gérant de stock
     */
    public function isStockManager(): bool
    {
        return $this->hasRole('gerant_stock');
    }

    /**
     * Vérifier si l'utilisateur est un vendeur
     */
    public function isSeller(): bool
    {
        return $this->hasRole('vendeur');
    }

    /**
     * Obtenir le nom du rôle principal
     */
    public function getRoleName(): string
    {
        return $this->roles->first()?->name ?? 'Aucun rôle';
    }

    /**
     * Obtenir le nom du rôle formaté pour l'affichage
     */
    public function getFormattedRoleName(): string
    {
        $roleName = $this->getRoleName();
        
        return match($roleName) {
            'administrateur' => 'Administrateur',
            'gerant_stock' => 'Gérant de Stock',
            'vendeur' => 'Vendeur',
            default => 'Utilisateur'
        };
    }

    /**
     * Vérifier si l'utilisateur est actif
     */
    public function isActive(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Activer l'utilisateur
     */
    public function activate(): bool
    {
        return $this->update(['statut' => 'actif']);
    }

    /**
     * Désactiver l'utilisateur
     */
    public function deactivate(): bool
    {
        return $this->update(['statut' => 'inactif']);
    }

    /**
     * Obtenir le badge de statut formaté
     */
    public function getStatutBadge(): string
    {
        return match($this->statut) {
            'actif' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>',
            'inactif' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactif</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inconnu</span>'
        };
    }
}