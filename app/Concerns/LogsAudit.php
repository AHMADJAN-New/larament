<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsAudit
{
    public static function bootLogsAudit(): void
    {
        static::created(function (Model $model): void {
            self::writeAuditLog($model, 'create', $model->attributesToArray());
        });

        static::updated(function (Model $model): void {
            $changes = $model->getChanges();

            unset($changes['updated_at']);

            if ($changes === []) {
                return;
            }

            self::writeAuditLog($model, 'update', $changes);
        });

        static::deleted(function (Model $model): void {
            self::writeAuditLog($model, 'delete', $model->attributesToArray());
        });
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private static function writeAuditLog(Model $model, string $action, array $meta): void
    {
        AuditLog::query()->create([
            'actor_user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $model::class,
            'entity_id' => (string) $model->getKey(),
            'meta' => $meta,
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
