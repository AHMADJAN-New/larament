<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Cache;
use OpenAI;
use RuntimeException;
use Throwable;

final class OpenAiTextService
{
    private const string MODELS_CACHE_KEY = 'openai_available_models';

    private const int MODELS_CACHE_TTL_SECONDS = 3600;

    private const string DEFAULT_REFINE_PROMPT = 'Refine and improve this text. Output only the refined text.';

    private const string DEFAULT_GENERATE_FROM_NOTES_PROMPT = 'From these meeting notes, extract the relevant content. Output only the extracted text.';

    public static function clearModelsCache(): void
    {
        Cache::forget(self::MODELS_CACHE_KEY);
    }

    /**
     * Fetch available models from the OpenAI API (cached). Falls back to config when no key or on error.
     *
     * @return array<string, string> Map of model id => label for select options
     */
    public function getAvailableModels(): array
    {
        $apiKey = $this->getApiKey();
        if ($apiKey === null || $apiKey === '') {
            return config('ai.models', []);
        }

        return Cache::remember(self::MODELS_CACHE_KEY, self::MODELS_CACHE_TTL_SECONDS, function () use ($apiKey): array {
            try {
                $client = OpenAI::client($apiKey);
                $response = $client->models()->list();
                $options = [];
                foreach ($response->data as $model) {
                    $id = $model->id;
                    $label = $model->ownedBy !== null && $model->ownedBy !== ''
                        ? "{$id} ({$model->ownedBy})"
                        : $id;
                    $options[$id] = $label;
                }
                ksort($options);

                return $options ?: config('ai.models', []);
            } catch (Throwable) {
                return config('ai.models', []);
            }
        });
    }

    public function isAvailable(): bool
    {
        if (AiSetting::get('ai_enabled', 'true') !== 'true') {
            return false;
        }

        return $this->getApiKey() !== null && $this->getApiKey() !== '';
    }

    /**
     * Refine text, optionally using meeting context for follow-up and task description.
     *
     * @param  array<string, string>  $context  Optional meeting context (e.g. agenda, notes, decisions_text, followup_text). Used when refining followup_text or description.
     */
    public function refine(string $text, string $fieldType, array $context = []): string
    {
        $prompts = config('ai.prompts.refine', []);
        if (! is_array($prompts) || ! array_key_exists($fieldType, $prompts)) {
            return $text;
        }

        $instruction = $prompts[$fieldType] ?? self::DEFAULT_REFINE_PROMPT;

        $userContent = $text;
        if ($context !== [] && in_array($fieldType, ['followup_text', 'description'], true)) {
            $userContent = $this->formatContextBlock($context)."\n\n".($fieldType === 'description' ? 'Task description to refine:' : 'Follow-up text to refine:')."\n\n".$text;
        }

        return $this->complete($instruction, $userContent);
    }

    public function generateFromNotes(string $notes, string $targetField): string
    {
        $prompts = config('ai.prompts.generate_from_notes', []);
        if (! is_array($prompts) || ! array_key_exists($targetField, $prompts)) {
            return '';
        }

        $instruction = $prompts[$targetField] ?? self::DEFAULT_GENERATE_FROM_NOTES_PROMPT;

        return $this->complete($instruction, $notes);
    }

    /**
     * @param  array<string, string>  $context
     */
    private function formatContextBlock(array $context): string
    {
        $labels = [
            'agenda' => 'اجنډا / Agenda',
            'notes' => 'نوټونه / Notes',
            'decisions_text' => 'پرېکړې / Decisions',
            'followup_text' => 'تعقيب / Follow-up',
        ];
        $lines = ['Meeting context:'];
        foreach ($context as $key => $value) {
            $value = is_string($value) ? mb_trim($value) : '';
            if ($value === '') {
                continue;
            }
            $label = $labels[$key] ?? $key;
            $lines[] = "--- {$label} ---";
            $lines[] = $value;
        }

        return implode("\n", $lines);
    }

    private function complete(string $systemInstruction, string $userContent): string
    {
        $apiKey = $this->getApiKey();
        if ($apiKey === null || $apiKey === '') {
            return $userContent;
        }

        $model = $this->getModel();
        $languageHint = AiSetting::get('ai_language_hint', 'auto');
        $languageNote = $languageHint !== 'auto'
            ? " The output should be in {$languageHint}."
            : ' Preserve the language of the input.';

        try {
            $client = OpenAI::client($apiKey);
            $response = $client->chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemInstruction.$languageNote,
                    ],
                    [
                        'role' => 'user',
                        'content' => $userContent,
                    ],
                ],
            ]);

            $content = $response->choices[0]->message->content;

            return $content !== null ? mb_trim($content) : $userContent;
        } catch (OpenAI\Exceptions\RateLimitException $e) {
            $retryAfter = $e->response->getHeaderLine('Retry-After');

            $errorMessage = null;
            $errorType = null;
            $errorCode = null;

            $rawBody = (string) $e->response->getBody();
            if ($rawBody !== '') {
                try {
                    /** @var array{error?: string|array{message?: string, type?: string, code?: string|int|null}} $data */
                    $data = json_decode($rawBody, true, flags: JSON_THROW_ON_ERROR);
                    $error = $data['error'] ?? null;

                    if (is_array($error)) {
                        $errorMessage = $error['message'] ?? null;
                        $errorType = $error['type'] ?? null;
                        $errorCode = $error['code'] ?? null;
                    } elseif (is_string($error)) {
                        $errorMessage = $error;
                    }
                } catch (Throwable) {
                    // Ignore JSON parse errors.
                }
            }

            $errorMessageLower = is_string($errorMessage) ? mb_strtolower($errorMessage) : '';
            $errorTypeLower = is_string($errorType) ? mb_strtolower($errorType) : '';
            $errorCodeLower = is_string($errorCode) ? mb_strtolower($errorCode) : '';

            $isQuotaProblem =
                in_array($errorCodeLower, ['insufficient_quota', 'billing_hard_limit_reached'], true) ||
                str_contains($errorTypeLower, 'insufficient_quota') ||
                str_contains($errorMessageLower, 'insufficient_quota') ||
                str_contains($errorMessageLower, 'quota') ||
                str_contains($errorMessageLower, 'billing');

            if ($isQuotaProblem) {
                throw new RuntimeException(
                    'ستاسې د OpenAI کوټه/کریډیټ ختم شوی. مهرباني وکړئ د Billing/Quota برخه وګورئ یا بل API Key وکاروئ.',
                    429,
                    $e
                );
            }

            $message = 'د OpenAI غوښتنې ډېرې شوې (Rate limit). مهرباني وکړئ لږ وروسته بیا هڅه وکړئ.';
            if ($retryAfter !== '') {
                $message .= " (Retry-After: {$retryAfter})";
            }

            throw new RuntimeException($message, 429, $e);
        } catch (OpenAI\Exceptions\ErrorException $e) {
            throw new RuntimeException(
                'د OpenAI غلطي: '.$e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        } catch (OpenAI\Exceptions\TransporterException $e) {
            throw new RuntimeException(
                'د OpenAI اتصال نشي. بیا هڅه وکړئ.',
                (int) $e->getCode(),
                $e
            );
        }
    }

    private function getApiKey(): ?string
    {
        $fromSettings = AiSetting::get('openai_api_key');
        if ($fromSettings !== null && $fromSettings !== '') {
            return $fromSettings;
        }

        $fromConfig = config('ai.api_key');
        if ($fromConfig !== null && $fromConfig !== '') {
            return $fromConfig;
        }

        return null;
    }

    private function getModel(): string
    {
        $model = AiSetting::get('openai_model');
        if ($model !== null && $model !== '') {
            return $model;
        }

        return (string) config('ai.default_model', 'gpt-4o-mini');
    }
}
