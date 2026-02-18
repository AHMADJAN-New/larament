<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Schemas;

use App\Enums\AttendanceStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Template;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

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
                                            ->columnSpanFull(),
                                        Textarea::make('notes')
                                            ->label('نوټونه')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Textarea::make('decisions_text')
                                            ->label('پرېکړې')
                                            ->rows(6)
                                            ->columnSpanFull(),
                                        Textarea::make('followup_text')
                                            ->label('تعقيب')
                                            ->rows(6)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(['default' => 1, 'md' => 2]),
                            ]),
                        Tab::make('حاضري')
                            ->schema([
                                Section::make('حاضري')
                                    ->schema([
                                        Repeater::make('attendees')
                                            ->label('ګډونوال')
                                            ->relationship()
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->addActionLabel('ګډونوال زیات کړئ')
                                            ->schema([
                                                Select::make('user_id')
                                                    ->label('ګډونوال')
                                                    ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                                                    ->searchable()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set): void {
                                                        $user = $state ? User::query()->find($state) : null;
                                                        $set('name', $user !== null ? $user->name : '');
                                                    }),
                                                TextInput::make('name')
                                                    ->label('نوم (که غړی په لست کې نه وي)')
                                                    ->maxLength(255)
                                                    ->required(fn (Get $get): bool => blank($get('user_id')))
                                                    ->visible(fn (Get $get): bool => blank($get('user_id'))),
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
                                                TextInput::make('owner')
                                                    ->label('مسؤل')
                                                    ->maxLength(255),
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
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(['default' => 1, 'md' => 2])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
