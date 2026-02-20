<?php

declare(strict_types=1);

namespace App\Filament\Resources\MeetingTasks\Tables;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\MeetingTask;
use DateTimeInterface;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class MeetingTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('due_date')
            ->columns([
                TextColumn::make('title')
                    ->label('موضوع/دنده')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('meeting.title')
                    ->label('مجلس')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('owner')
                    ->label('مسؤل')
                    ->alignment(Alignment::Center)
                    ->searchable(),
                TextColumn::make('due_date')
                    ->label('وروستۍ نېټه')
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn (\Carbon\Carbon|DateTimeInterface|string|null $state): string => shamsi_date($state))
                    ->sortable()
                    ->color(fn (MeetingTask $record): ?string => $record->is_overdue ? 'danger' : null),
                TextColumn::make('priority')
                    ->label('اولویت')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->formatStateUsing(function (TaskPriority|string $state): string {
                        $priority = $state instanceof TaskPriority ? $state : TaskPriority::from($state);

                        return $priority->label();
                    })
                    ->color(function (TaskPriority|string $state): string {
                        $priority = $state instanceof TaskPriority ? $state : TaskPriority::from($state);

                        return match ($priority) {
                            TaskPriority::High => 'danger',
                            TaskPriority::Medium => 'warning',
                            TaskPriority::Low => 'success',
                        };
                    }),
                TextColumn::make('status')
                    ->label('حالت')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->formatStateUsing(function (TaskStatus|string $state): string {
                        $status = $state instanceof TaskStatus ? $state : TaskStatus::from($state);

                        return $status->label();
                    })
                    ->color(function (TaskStatus|string $state): string {
                        $status = $state instanceof TaskStatus ? $state : TaskStatus::from($state);

                        return match ($status) {
                            TaskStatus::Open => 'gray',
                            TaskStatus::InProgress => 'warning',
                            TaskStatus::Done => 'success',
                            TaskStatus::Blocked => 'danger',
                        };
                    }),
                TextColumn::make('updated_at')
                    ->label('وروستی بدلون')
                    ->alignment(Alignment::Center)
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('حالت')
                    ->options(TaskStatus::options()),
                SelectFilter::make('priority')
                    ->label('اولویت')
                    ->options(TaskPriority::options()),
                Filter::make('owner')
                    ->label('مسؤل')
                    ->form([
                        TextInput::make('owner')
                            ->label('نوم'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['owner'] ?? null),
                        fn (Builder $query): Builder => $query->where('owner', 'like', '%'.$data['owner'].'%'),
                    )),
                Filter::make('overdue')
                    ->label('ناوخته')
                    ->query(
                        fn (Builder $query): Builder => $query
                            ->whereNotNull('due_date')
                            ->whereDate('due_date', '<', now()->toDateString())
                            ->where('status', '!=', TaskStatus::Done->value),
                    ),
            ])
            ->recordActions([
                EditAction::make()->label('سمون'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('ډله‌ییز ړنګول'),
                ]),
            ]);
    }
}
