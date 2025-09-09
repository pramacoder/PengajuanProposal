<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_identifier',
        'user_type',
        'title',
        'message',
        'type',
        'data',
        'read_at',
        'proposal_id'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Relasi ke User berdasarkan tipe dan identifier
     */
    public function user()
    {
        switch ($this->user_type) {
            case 'mahasiswa':
                return $this->belongsTo(Mahasiswa::class, 'user_identifier', 'nim');
            case 'dosen':
                return $this->belongsTo(Dosen::class, 'user_identifier', 'nidn');
            case 'reviewer':
                return $this->belongsTo(Reviewer::class, 'user_identifier', 'id_reviewer');
            case 'operator':
                return $this->belongsTo(PT::class, 'user_identifier', 'id_pt');
            default:
                return null;
        }
    }

    /**
     * Relasi ke Proposal (opsional)
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id', 'id_proposal');
    }

    /**
     * Scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope untuk notifikasi yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope untuk notifikasi berdasarkan user type
     */
    public function scopeForUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope untuk notifikasi berdasarkan user identifier
     */
    public function scopeForUser($query, $userIdentifier)
    {
        return $query->where('user_identifier', $userIdentifier);
    }

    /**
     * Cek apakah notifikasi sudah dibaca
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Cek apakah notifikasi belum dibaca
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead(): bool
    {
        $this->update(['read_at' => Carbon::now()]);
        return true;
    }

    /**
     * Tandai notifikasi sebagai belum dibaca
     */
    public function markAsUnread(): bool
    {
        $this->update(['read_at' => null]);
        return true;
    }

    /**
     * Get time ago in Indonesian
     */
    public function getTimeAgoAttribute(): string
    {
        $now = Carbon::now();
        $diff = $this->created_at->diff($now);
        
        if ($diff->y > 0) {
            return $diff->y . ' tahun yang lalu';
        } elseif ($diff->m > 0) {
            return $diff->m . ' bulan yang lalu';
        } elseif ($diff->d > 0) {
            return $diff->d . ' hari yang lalu';
        } elseif ($diff->h > 0) {
            return $diff->h . ' jam yang lalu';
        } elseif ($diff->i > 0) {
            return $diff->i . ' menit yang lalu';
        } else {
            return 'Baru saja';
        }
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'info' => 'info-circle',
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'danger' => 'times-circle',
            'primary' => 'bell'
        ];

        return $icons[$this->type] ?? 'bell';
    }

    /**
     * Get notification color class based on type
     */
    public function getColorClassAttribute(): string
    {
        $colors = [
            'info' => 'text-blue-600',
            'success' => 'text-green-600',
            'warning' => 'text-yellow-600',
            'danger' => 'text-red-600',
            'primary' => 'text-indigo-600'
        ];

        return $colors[$this->type] ?? 'text-gray-600';
    }
}
