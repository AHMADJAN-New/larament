<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\AiSetting;
use App\Services\OpenAiTextService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
final class AiSettings extends Page
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    protected static string|UnitEnum|null $navigationGroup = 'سیسټم';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'AI تنظیمات';

    protected static ?int $navigationSort = 25;

    protected string $view = 'filament.pages.ai-settings';

    public function mount(): void
    {
        $this->form->fill([
            'ai_enabled' => AiSetting::get('ai_enabled', 'true') === 'true',
            'openai_api_key' => '',
            'openai_model' => AiSetting::get('openai_model') ?? config('ai.default_model', 'gpt-4o-mini'),
            'ai_language_hint' => AiSetting::get('ai_language_hint') ?? 'auto',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('OpenAI')
                    ->description('د مجلس متنونو لپاره د AI ښه کولو تنظیمات.')
                    ->schema([
                        Toggle::make('ai_enabled')
                            ->label('AI فعال')
                            ->default(true),
                        TextInput::make('openai_api_key')
                            ->label('API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('خالي پریښئ که مخکینی ساتل شوی وي')
                            ->maxLength(255)
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        Select::make('openai_model')
                            ->label('ماډل')
                            ->options(fn (): array => app(OpenAiTextService::class)->getAvailableModels())
                            ->required()
                            ->default('gpt-4o-mini')
                            ->searchable(),
                        Select::make('ai_language_hint')
                            ->label('د ژبې اشاره')
                            ->options(config('ai.language_hints', []))
                            ->default('auto'),
                    ])
                    ->columns(1),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([$this->getFormComponent()])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('خوندي کول')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        AiSetting::set('ai_enabled', $data['ai_enabled'] ? 'true' : 'false');
        if (array_key_exists('openai_api_key', $data) && filled($data['openai_api_key'])) {
            AiSetting::set('openai_api_key', $data['openai_api_key']);
        }
        AiSetting::set('openai_model', $data['openai_model']);
        AiSetting::set('ai_language_hint', $data['ai_language_hint']);

        OpenAiTextService::clearModelsCache();

        Notification::make()
            ->success()
            ->title('تنظیمات خوندي شول')
            ->send();
    }

    protected function getFormComponent(): EmbeddedSchema
    {
        return EmbeddedSchema::make('form');
    }
}
