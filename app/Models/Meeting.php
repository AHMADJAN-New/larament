<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

final class Meeting extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingFactory> */
    use HasFactory, LogsAudit;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'meeting_no',
        'title',
        'date',
        'time',
        'location',
        'agenda',
        'notes',
        'decisions_text',
        'followup_text',
        'is_confidential',
        'created_by',
        'updated_by',
    ];

    public function attendees(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(MeetingTask::class);
    }

    public function shareLinks(): HasMany
    {
        return $this->hasMany(ShareLink::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendees()
            ->where('status', 'present')
            ->count();
    }

    public function getAbsentCountAttribute(): int
    {
        return $this->attendees()
            ->where('status', 'absent')
            ->count();
    }

    protected static function booted(): void
    {
        self::creating(function (Meeting $meeting): void {
            if (! filled($meeting->meeting_no)) {
                $meeting->meeting_no = (int) (self::query()->max('meeting_no') ?? 0) + 1;
            }

            if (! filled($meeting->created_by)) {
                $meeting->created_by = Auth::id();
            }

            $meeting->updated_by = Auth::id();
        });

        self::updating(function (Meeting $meeting): void {
            $meeting->updated_by = Auth::id();
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_confidential' => 'bool',
        ];
    }
}
