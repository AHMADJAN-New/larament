<?php

declare(strict_types=1);

use App\Models\AiSetting;
use App\Services\OpenAiTextService;

beforeEach(function () {
    AiSetting::set('ai_enabled', 'false');
    AiSetting::set('openai_api_key', null);
    config(['ai.api_key' => null]);
});

it('is not available when AI is disabled', function () {
    AiSetting::set('ai_enabled', 'false');

    expect(app(OpenAiTextService::class)->isAvailable())->toBeFalse();
});

it('is not available when API key is missing', function () {
    AiSetting::set('ai_enabled', 'true');
    AiSetting::set('openai_api_key', null);

    expect(app(OpenAiTextService::class)->isAvailable())->toBeFalse();
});

it('is available when enabled and API key is set', function () {
    AiSetting::set('ai_enabled', 'true');
    AiSetting::set('openai_api_key', 'sk-test');

    expect(app(OpenAiTextService::class)->isAvailable())->toBeTrue();
});

it('refine returns original text when service is not available', function () {
    $service = app(OpenAiTextService::class);
    $text = 'Some rough notes';

    $result = $service->refine($text, 'notes');

    expect($result)->toBe($text);
});

it('generateFromNotes returns empty string for invalid target field', function () {
    AiSetting::set('ai_enabled', 'true');
    AiSetting::set('openai_api_key', 'sk-test');
    $service = app(OpenAiTextService::class);

    $result = $service->generateFromNotes('Some notes', 'invalid_field');

    expect($result)->toBe('');
});

it('refine returns original text for unknown field type', function () {
    AiSetting::set('ai_enabled', 'true');
    AiSetting::set('openai_api_key', 'sk-test');
    config(['ai.api_key' => null]);
    $service = app(OpenAiTextService::class);
    $text = 'Some text';

    $result = $service->refine($text, 'unknown_field');

    expect($result)->toBe($text);
});

it('refines text via API when key is configured', function () {
    $apiKey = config('ai.api_key') ?: env('OPENAI_API_KEY');
    if (empty($apiKey)) {
        test()->markTestSkipped('Set OPENAI_API_KEY to run OpenAI integration test');
    }

    AiSetting::set('ai_enabled', 'true');
    AiSetting::set('openai_api_key', $apiKey);
    AiSetting::set('openai_model', 'gpt-4o-mini');
    $service = app(OpenAiTextService::class);

    expect($service->isAvailable())->toBeTrue();

    $result = null;
    try {
        $result = $service->refine('Discuss budget. Review timeline.', 'agenda');
    } catch (RuntimeException $e) {
        if ($e->getCode() === 429) {
            test()->markTestSkipped($e->getMessage());
        }

        throw $e;
    }

    expect($result)->not->toBeNull()
        ->and($result)->not->toBe('Discuss budget. Review timeline.')
        ->and($result)->not->toBeEmpty()
        ->and(mb_strlen($result))->toBeGreaterThan(20);
})->group('openai');
