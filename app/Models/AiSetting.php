<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

final class AiSetting extends Model
{
    /**
     * Keys that should be encrypted when stored.
     *
     * @var list<string>
     */
    private const array ENCRYPTED_KEYS = [
        'openai_api_key',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'key';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = self::query()->where('key', $key)->first();

        if ($row === null) {
            return $default;
        }

        $value = $row->value;

        if (in_array($key, self::ENCRYPTED_KEYS, true) && $value !== null && $value !== '') {
            try {
                return Crypt::decryptString($value);
            } catch (Throwable) {
                return $default;
            }
        }

        return $value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $valueToStore = $value === null ? null : (string) $value;

        if (in_array($key, self::ENCRYPTED_KEYS, true) && $valueToStore !== null && $valueToStore !== '') {
            $valueToStore = Crypt::encryptString($valueToStore);
        }

        $row = self::query()->firstOrNew(['key' => $key]);
        $row->value = $valueToStore;
        $row->save();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }
}
