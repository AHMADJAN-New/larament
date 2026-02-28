<?php

declare(strict_types=1);

namespace App\Filament\Resources\MeetingTasks\Schemas;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\MeetingAttendee;
use App\Models\MeetingTask;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

final class MeetingTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('meeting_id')
                    ->label('مجلس')
                    ->relationship('meeting', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->disabled(fn (): bool => auth()->user()?->canEditAnyTask() !== true),
                TextInput::make('title')
                    ->label('موضوع/دنده')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (): bool => auth()->user()?->canEditAnyTask() !== true),
                Select::make('owner')
                    ->label('مسؤل')
                    ->options(function (Get $get, ?MeetingTask $record): array {
                        $meetingId = $record?->meeting_id ?? $get('meeting_id');
                        if (blank($meetingId)) {
                            return [];
                        }

                        return MeetingAttendee::query()
                            ->where('meeting_id', $meetingId)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn (MeetingAttendee $a): array => [$a->display_name => $a->display_name])
                            ->all();
                    })
                    ->searchable()
                    ->nullable()
                    ->disabled(fn (): bool => auth()->user()?->canEditAnyTask() !== true),
                DatePicker::make('due_date')
                    ->label('وروستۍ نېټه')
                    ->disabled(fn (): bool => auth()->user()?->canEditAnyTask() !== true),
                Select::make('priority')
                    ->label('اولویت')
                    ->options(TaskPriority::options())
                    ->default(TaskPriority::Medium->value)
                    ->required()
                    ->disabled(fn (): bool => auth()->user()?->canEditAnyTask() !== true),
                Select::make('status')
                    ->label('حالت')
                    ->options(TaskStatus::options())
                    ->default(TaskStatus::Open->value)
                    ->required(),
                Textarea::make('description')
                    ->label('تشریح')
                    ->rows(4)
                    ->columnSpanFull()
                    ->disabled(fn (): bool => auth()->user()?->canEditAnyTask() !== true),
            ])
            ->columns(2);
    }
}
