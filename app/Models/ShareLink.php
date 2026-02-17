<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class ShareLink extends Model
{
    /** @use HasFactory<\Database\Factories\ShareLinkFactory> */
    use HasFactory, LogsAudit;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'meeting_id',
        'token',
        'expires_at',
        'password_hash',
        'created_by',
        'created_at',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    protected static function booted(): void
    {
        self::creating(function (ShareLink $shareLink): void {
            if (! filled($shareLink->token)) {
                $shareLink->token = Str::random(64);
            }

            if (! filled($shareLink->created_by)) {
                $shareLink->created_by = Auth::id();
            }

            if (! filled($shareLink->created_at)) {
                $shareLink->created_at = now();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
