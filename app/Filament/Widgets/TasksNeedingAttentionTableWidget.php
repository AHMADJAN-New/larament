<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Filament\Resources\MeetingTasks\MeetingTaskResource;
use App\Models\MeetingTask;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

final class TasksNeedingAttentionTableWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'کارونه چې پاملرنه اړینه ده';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MeetingTask::query()
                    ->whereNot('status', TaskStatus::Done)
                    ->with('meeting')
                    ->orderBy('due_date')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('دنده')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->limit(40),
                TextColumn::make('meeting.title')
                    ->label('مجلس')
                    ->alignment(Alignment::Center)
                    ->limit(25)
                    ->url(fn (MeetingTask $record): ?string => $record->meeting_id
                        ? \App\Filament\Resources\Meetings\MeetingResource::getUrl('edit', ['record' => $record->meeting_id])
                        : null),
                TextColumn::make('owner')
                    ->label('مسؤل')
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('due_date')
                    ->label('وروستۍ نېټه')
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn ($state): string => $state ? shamsi_date($state) : '—')
                    ->sortable()
                    ->color(fn (MeetingTask $record): string => $record->is_overdue ? 'danger' : 'gray'),
                TextColumn::make('priority')
                    ->label('اولویت')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->formatStateUsing(fn (TaskPriority|string $state): string => ($state instanceof TaskPriority ? $state : TaskPriority::from($state))->label())
                    ->color(fn (TaskPriority|string $state): string => match ($state instanceof TaskPriority ? $state : TaskPriority::from($state)) {
                        TaskPriority::High => 'danger',
                        TaskPriority::Medium => 'warning',
                        TaskPriority::Low => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('حالت')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->formatStateUsing(fn (TaskStatus|string $state): string => ($state instanceof TaskStatus ? $state : TaskStatus::from($state))->label())
                    ->color(fn (TaskStatus|string $state): string => match ($state instanceof TaskStatus ? $state : TaskStatus::from($state)) {
                        TaskStatus::Open => 'gray',
                        TaskStatus::InProgress => 'info',
                        TaskStatus::Blocked => 'danger',
                        TaskStatus::Done => 'success',
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('سمون')
                    ->url(fn (MeetingTask $record): string => MeetingTaskResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
