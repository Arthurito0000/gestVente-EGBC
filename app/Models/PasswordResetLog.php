<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'action',
        'status',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Créer un log de tentative de reset
     */
    public static function logRequest($email, $ipAddress, $userAgent, $status = 'success')
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'action' => 'request',
            'status' => $status,
            'details' => [
                'timestamp' => Carbon::now()->toISOString(),
                'browser' => self::getBrowserInfo($userAgent),
            ],
        ]);
    }

    /**
     * Créer un log de reset réussi
     */
    public static function logReset($email, $ipAddress, $userAgent, $status = 'success')
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'action' => 'reset',
            'status' => $status,
            'details' => [
                'timestamp' => Carbon::now()->toISOString(),
                'browser' => self::getBrowserInfo($userAgent),
            ],
        ]);
    }

    /**
     * Créer un log d'échec
     */
    public static function logFailed($email, $ipAddress, $userAgent, $reason = 'unknown')
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'action' => 'failed',
            'status' => 'failed',
            'details' => [
                'timestamp' => Carbon::now()->toISOString(),
                'browser' => self::getBrowserInfo($userAgent),
                'reason' => $reason,
            ],
        ]);
    }

    /**
     * Obtenir les statistiques de sécurité
     */
    public static function getSecurityStats($days = 30)
    {
        $since = Carbon::now()->subDays($days);

        return [
            'total_requests' => self::where('action', 'request')
                ->where('created_at', '>=', $since)
                ->count(),
            'successful_resets' => self::where('action', 'reset')
                ->where('status', 'success')
                ->where('created_at', '>=', $since)
                ->count(),
            'failed_attempts' => self::where('action', 'failed')
                ->where('created_at', '>=', $since)
                ->count(),
            'unique_ips' => self::where('created_at', '>=', $since)
                ->distinct('ip_address')
                ->count(),
            'suspicious_ips' => self::where('created_at', '>=', $since)
                ->selectRaw('ip_address, COUNT(*) as attempts')
                ->groupBy('ip_address')
                ->having('attempts', '>', 5)
                ->get(),
        ];
    }

    /**
     * Obtenir les tentatives récentes pour un email
     */
    public static function getRecentAttempts($email, $hours = 24)
    {
        return self::where('email', $email)
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Vérifier si une IP est suspecte
     */
    public static function isSuspiciousIP($ipAddress, $hours = 1, $maxAttempts = 5)
    {
        $attempts = self::where('ip_address', $ipAddress)
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->count();

        return $attempts >= $maxAttempts;
    }

    /**
     * Extraire les informations du navigateur
     */
    private static function getBrowserInfo($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Google Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Microsoft Edge';
        } else {
            return 'Navigateur inconnu';
        }
    }

    /**
     * Scope pour les tentatives récentes
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    /**
     * Scope pour les tentatives suspectes
     */
    public function scopeSuspicious($query)
    {
        return $query->where('action', 'failed')
            ->orWhere(function($q) {
                $q->selectRaw('COUNT(*)')
                  ->from('password_reset_logs as prl2')
                  ->whereColumn('prl2.ip_address', 'password_reset_logs.ip_address')
                  ->where('prl2.created_at', '>=', Carbon::now()->subHour())
                  ->havingRaw('COUNT(*) > 5');
            });
    }
}
