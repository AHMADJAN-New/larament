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
        'name',
        'status',
        'reason',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
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
