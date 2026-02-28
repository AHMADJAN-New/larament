<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Schemas;

use App\Enums\AttendanceStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Attendee;
use App\Models\MeetingAttendee;
use App\Models\Template;
use App\Models\User;
use App\Services\OpenAiTextService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use RuntimeException;

final class MeetingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make('معلومات')
                            ->schema([
                                Section::make('د مجلس معلومات')
                                    ->schema([
                                        Select::make('template_id')
                                            ->label('نمونه')
                                            ->options(fn (): array => Template::query()->orderBy('name')->pluck('name', 'id')->all())
                                            ->searchable()
                                            ->dehydrated(false)
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set): void {
                                                if (blank($state)) {
                                                    return;
                                                }

                                                $template = Template::query()->find($state);

                                                if ($template === null) {
                                                    return;
                                                }

                                                if (filled($template->meeting_title_default)) {
                                                    $set('title', $template->meeting_title_default);
                                                }

                                                $set('agenda', $template->agenda_template);
                                                $set('notes', $template->notes_template);
                                                $set('decisions_text', $template->decisions_template);
                                                $set('followup_text', $template->followup_template);

                                                /** @var array<int, array<string, mixed>>|null $tasksTemplate */
                                                $tasksTemplate = $template->tasks_template;
                                                if (is_array($tasksTemplate) && $tasksTemplate !== []) {
                                                    $tasks = [];

                                                    foreach ($tasksTemplate as $templateTask) {
                                                        $tasks[] = [
                                                            'title' => $templateTask['title'] ?? null,
                                                            'owner' => $templateTask['owner'] ?? null,
                                                            'due_date' => $templateTask['due_date'] ?? null,
                                                            'priority' => $templateTask['priority'] ?? TaskPriority::Medium->value,
                                                            'status' => $templateTask['status'] ?? TaskStatus::Open->value,
                                                            'description' => $templateTask['description'] ?? null,
                                                        ];
                                                    }

                                                    $set('tasks', $tasks);
                                                }
                                            }),
                                        TextInput::make('meeting_no')
                                            ->label('د مجلس شمېره')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('title')
                                            ->label('عنوان')
                                            ->required()
                                            ->maxLength(255),
                                        DatePicker::make('date')
                                            ->label('نېټه')
                                            ->default(now())
                                            ->required(),
                                        TimePicker::make('time')
                                            ->label('وخت')
                                            ->seconds(false),
                                        TextInput::make('location')
                                            ->label('ځای')
                                            ->maxLength(255),
                                        Toggle::make('is_confidential')
                                            ->label('محرم مجلس')
                                            ->default(false),
                                        Textarea::make('agenda')
                                            ->label('اجنډا')
                                            ->rows(4)
                                            ->columnSpanFull()
                                            ->hintAction(self::makeRefineAction('agenda', 'اجنډا')),
                                        Textarea::make('notes')
                                            ->label('نوټونه')
                                            ->rows(4)
                                            ->columnSpanFull()
                                            ->hintAction(self::makeRefineAction('notes', 'نوټونه')),
                                        Textarea::make('decisions_text')
                                            ->label('پرېکړې')
                                            ->rows(6)
                                            ->columnSpanFull()
                                            ->hintActions([
                                                self::makeRefineAction('decisions_text', 'پرېکړې'),
                                                self::makeGenerateFromNotesAction('decisions_text', 'پرېکړې'),
                                            ]),
                                        Textarea::make('followup_text')
                                            ->label('تعقيب')
                                            ->rows(6)
                                            ->columnSpanFull()
                                            ->hintActions([
                                                self::makeRefineAction('followup_text', 'تعقيب', ['agenda', 'notes', 'decisions_text']),
                                                self::makeGenerateFromNotesAction('followup_text', 'تعقيب'),
                                            ]),
                                    ])
                                    ->columns(['default' => 1, 'md' => 2]),
                            ]),
                        Tab::make('حاضري')
                            ->schema([
                                Section::make('حاضري')
                                    ->schema([
                                        Repeater::make('attendees')
                                            ->label('د مجلس غړي')
                                            ->relationship()
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('د مجلس غړی زیات کړئ')
                                            ->schema([
                                                Select::make('frequent_attendee_id')
                                                    ->label('له لست څخه')
                                                    ->options(fn (): array => Attendee::query()
                                                        ->orderBy('name')
                                                        ->get()
                                                        ->mapWithKeys(fn (Attendee $a): array => [$a->getKey() => $a->display_label])
                                                        ->all())
                                                    ->searchable()
                                                    ->dehydrated(false)
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set): void {
                                                        if (blank($state)) {
                                                            return;
                                                        }
                                                        $attendee = Attendee::query()->with('user')->find($state);
                                                        if ($attendee === null) {
                                                            return;
                                                        }
                                                        $set('user_id', $attendee->user_id);
                                                        $set('name', $attendee->user?->name ?? $attendee->name);
                                                    }),
                                                Select::make('user_id')
                                                    ->label('غړی')
                                                    ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                                                    ->searchable()
                                                    ->live()
                                                    ->visible(fn (Get $get): bool => blank($get('frequent_attendee_id')))
                                                    ->afterStateUpdated(function ($state, Set $set): void {
                                                        $user = $state ? User::query()->find($state) : null;
                                                        $set('name', $user !== null ? $user->name : '');
                                                    }),
                                                TextInput::make('name')
                                                    ->label('نوم (که غړی په لست کې نه وي)')
                                                    ->maxLength(255)
                                                    ->required(fn (Get $get): bool => blank($get('user_id')) && blank($get('frequent_attendee_id')))
                                                    ->visible(fn (Get $get): bool => blank($get('frequent_attendee_id')) && blank($get('user_id'))),
                                                Select::make('status')
                                                    ->label('حالت')
                                                    ->options(AttendanceStatus::options())
                                                    ->default(AttendanceStatus::Present->value)
                                                    ->live()
                                                    ->required(),
                                                TextInput::make('reason')
                                                    ->label('علت')
                                                    ->maxLength(255)
                                                    ->visible(fn (Get $get): bool => $get('status') === AttendanceStatus::Absent->value),
                                            ])
                                            ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('کارونه')
                            ->schema([
                                Section::make('تعقيب / کارونه')
                                    ->schema([
                                        Repeater::make('tasks')
                                            ->label('کارونه')
                                            ->relationship()
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('دنده زیات کړئ')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('موضوع/دنده')
                                                    ->required()
                                                    ->maxLength(255),
                                                Select::make('owner')
                                                    ->label('مسؤل')
                                                    ->options(function (Get $get, $record): array {
                                                        if ($record === null || ! $record->exists) {
                                                            return [];
                                                        }

                                                        $meeting = $record->meeting;
                                                        if ($meeting === null) {
                                                            return [];
                                                        }

                                                        return $meeting->attendees()
                                                            ->orderBy('name')
                                                            ->get()
                                                            ->mapWithKeys(fn (MeetingAttendee $a): array => [$a->display_name => $a->display_name])
                                                            ->all();
                                                    })
                                                    ->searchable()
                                                    ->nullable(),
                                                DatePicker::make('due_date')
                                                    ->label('وروستۍ نېټه'),
                                                Select::make('priority')
                                                    ->label('اولویت')
                                                    ->options(TaskPriority::options())
                                                    ->default(TaskPriority::Medium->value)
                                                    ->required(),
                                                Select::make('status')
                                                    ->label('حالت')
                                                    ->options(TaskStatus::options())
                                                    ->default(TaskStatus::Open->value)
                                                    ->required(),
                                                Textarea::make('description')
                                                    ->label('تشریح')
                                                    ->rows(3)
                                                    ->columnSpanFull()
                                                    ->hintAction(self::makeRefineAction('description', 'تشریح', ['agenda', 'notes', 'decisions_text', 'followup_text'])),
                                            ])
                                            ->columns(['default' => 1, 'md' => 2])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * @param  array<int, string>  $contextKeys  Form field names to pass as meeting context (e.g. agenda, notes, decisions_text, followup_text). Only used for followup_text and description.
     */
    private static function makeRefineAction(string $field, string $label, array $contextKeys = []): Action
    {
        $service = app(OpenAiTextService::class);

        return Action::make("refine_{$field}")
            ->label('AI سره ښه کول')
            ->tooltip("AI سره {$label} ښه کول")
            ->icon(Heroicon::OutlinedSparkles)
            ->color('gray')
            ->size('sm')
            ->iconButton()
            ->hiddenLabel()
            ->visible(fn (Get $get): bool => filled($get($field)) && $service->isAvailable())
            ->fillForm(function (Get $get) use ($field, $service, $contextKeys): array {
                $current = $get($field) ?? '';
                $context = [];
                foreach ($contextKeys as $key) {
                    $value = $get($key);
                    if (is_string($value) && mb_trim($value) !== '') {
                        $context[$key] = $value;
                    }
                }
                try {
                    $refined = $service->refine($current, $field, $context);
                } catch (RuntimeException $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->send();

                    return ['refined_text' => $current];
                }

                return ['refined_text' => $refined];
            })
            ->schema([
                Textarea::make('refined_text')
                    ->label('ښه شوی متن')
                    ->rows(8)
                    ->required(),
            ])
            ->modalHeading("AI سره {$label} ښه کول")
            ->modalSubmitActionLabel('قبول کول')
            ->action(function (array $data, Set $set) use ($field): void {
                $set($field, $data['refined_text']);
            });
    }

    private static function makeGenerateFromNotesAction(string $targetField, string $label): Action
    {
        $service = app(OpenAiTextService::class);

        return Action::make("generate_{$targetField}_from_notes")
            ->label('له نوټونو څخه جوړول')
            ->tooltip("له نوټونو څخه {$label} جوړول")
            ->icon(Heroicon::OutlinedDocumentText)
            ->color('gray')
            ->size('sm')
            ->iconButton()
            ->hiddenLabel()
            ->visible(fn (Get $get): bool => filled($get('notes')) && $service->isAvailable())
            ->fillForm(function (Get $get) use ($targetField, $service): array {
                $notes = $get('notes') ?? '';
                try {
                    $generated = $service->generateFromNotes($notes, $targetField);
                } catch (RuntimeException $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->send();

                    return ['generated_text' => ''];
                }

                return ['generated_text' => $generated];
            })
            ->schema([
                Textarea::make('generated_text')
                    ->label($label)
                    ->rows(8)
                    ->required(),
            ])
            ->modalHeading("له نوټونو څخه {$label}")
            ->modalSubmitActionLabel('قبول کول')
            ->action(function (array $data, Set $set) use ($targetField): void {
                $set($targetField, $data['generated_text']);
            });
    }
}
