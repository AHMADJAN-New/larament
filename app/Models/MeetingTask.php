<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsAudit;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

final class MeetingTask extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingTaskFactory> */
    use HasFactory, LogsAudit;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'meeting_id',
        'title',
        'owner',
        'due_date',
        'priority',
        'status',
        'description',
        'created_by',
        'updated_by',
        'closed_at',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->due_date === null) {
            return false;
        }

        return $this->status !== TaskStatus::Done && $this->due_date->isPast();
    }

    protected static function booted(): void
    {
        self::creating(function (MeetingTask $task): void {
            if (! filled($task->created_by)) {
                $task->created_by = Auth::id();
            }

            $task->updated_by = Auth::id();

            if ($task->status === TaskStatus::Done && blank($task->closed_at)) {
                $task->closed_at = now();
            }
        });

        self::updating(function (MeetingTask $task): void {
            $task->updated_by = Auth::id();

            if ($task->status === TaskStatus::Done && blank($task->closed_at)) {
                $task->closed_at = now();
            }

            if ($task->status !== TaskStatus::Done) {
                $task->closed_at = null;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'closed_at' => 'datetime',
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
        ];
    }
}
