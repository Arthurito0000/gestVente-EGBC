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
}