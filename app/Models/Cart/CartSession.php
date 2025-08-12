<?php

namespace App\Models\Cart;

use App\Models\Cart\Cart;
use Illuminate\Database\Eloquent\Model;

class CartSession extends Model
{
    protected $table = 'cart_session';

    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'expires_at'
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'session_id', 'session_id');
    }

    // Check if session is expired
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Update last activity
    public function updateActivity()
    {
        $this->update([
            'last_activity' => now(),
            'expires_at' => now()->addDays(30) // Cart expires after 30 days
        ]);
    }

    // Clean expired sessions
    public static function cleanExpiredSessions()
    {
        return self::where('expires_at', '<', now())->delete();
    }
}
