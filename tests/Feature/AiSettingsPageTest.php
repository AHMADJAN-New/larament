<?php

declare(strict_types=1);

use App\Filament\Pages\AiSettings;
use App\Models\AiSetting;

use function Pest\Livewire\livewire;

it('can render the AI settings page', function () {
    livewire(AiSettings::class)
        ->assertOk();
});

it('can load existing AI settings into the form', function () {
    AiSetting::set('ai_enabled', 'true');
    AiSetting::set('openai_model', 'gpt-4o');
    AiSetting::set('ai_language_hint', 'pashto');

    livewire(AiSettings::class)
        ->assertFormSet([
            'ai_enabled' => true,
            'openai_model' => 'gpt-4o',
            'ai_language_hint' => 'pashto',
        ]);
});

it('can save AI settings', function () {
    livewire(AiSettings::class)
        ->fillForm([
            'ai_enabled' => true,
            'openai_model' => 'gpt-4o-mini',
            'ai_language_hint' => 'english',
        ])
        ->call('save')
        ->assertNotified();

    expect(AiSetting::get('ai_enabled'))->toBe('true');
    expect(AiSetting::get('openai_model'))->toBe('gpt-4o-mini');
    expect(AiSetting::get('ai_language_hint'))->toBe('english');
});

it('persists API key when provided', function () {
    livewire(AiSettings::class)
        ->fillForm([
            'ai_enabled' => true,
            'openai_api_key' => 'sk-test-key-123',
            'openai_model' => 'gpt-4o-mini',
            'ai_language_hint' => 'auto',
        ])
        ->call('save')
        ->assertNotified();

    expect(AiSetting::get('openai_api_key'))->toBe('sk-test-key-123');
});

it('can disable AI', function () {
    AiSetting::set('ai_enabled', 'true');

    livewire(AiSettings::class)
        ->fillForm([
            'ai_enabled' => false,
            'openai_model' => 'gpt-4o-mini',
            'ai_language_hint' => 'auto',
        ])
        ->call('save')
        ->assertNotified();

    expect(AiSetting::get('ai_enabled'))->toBe('false');
});
