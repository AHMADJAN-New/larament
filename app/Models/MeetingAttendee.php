<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsAudit;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MeetingAttendee extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingAttendeeFactory> */
    use HasFactory, LogsAudit;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'meeting_id',
        'user_id',
        'name',
        'status',
        'reason',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $userId = $this->getRawOriginal('user_id');

        if ($userId !== null) {
            /** @var User|null $user */
            $user = $this->user;

            if ($user !== null) {
                return $user->name;
            }
        }

        return (string) $this->name;
    }

    protected static function booted(): void
    {
        self::saving(function (MeetingAttendee $attendee): void {
            $userId = $attendee->getRawOriginal('user_id');
            if ($userId && blank($attendee->name)) {
                /** @var User|null $user */
                $user = $attendee->user ?? User::query()->find($userId);
                $attendee->name = $user !== null ? $user->name : '';
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
        ];
    }
}
