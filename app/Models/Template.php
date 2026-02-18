<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\LogsAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

final class Template extends Model
{
    /** @use HasFactory<\Database\Factories\TemplateFactory> */
    use HasFactory, LogsAudit;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'meeting_title_default',
        'agenda_template',
        'notes_template',
        'decisions_template',
        'followup_template',
        'tasks_template',
        'created_by',
        'updated_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function booted(): void
    {
        self::creating(function (Template $template): void {
            if (! filled($template->created_by)) {
                $template->created_by = Auth::id();
            }

            $template->updated_by = Auth::id();
        });

        self::updating(function (Template $template): void {
            $template->updated_by = Auth::id();
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tasks_template' => 'array',
        ];
    }
}
