<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Tables;

use App\Enums\TaskStatus;
use App\Models\Meeting;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class MeetingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('meeting_no')
                    ->label('شمېره')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->label('عنوان')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->label('نېټه')
                    ->date()
                    ->sortable(),
                TextColumn::make('time')
                    ->label('وخت')
                    ->time(),
                TextColumn::make('location')
                    ->label('ځای')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('attendees_count')
                    ->label('ګډونوال')
                    ->counts('attendees'),
                TextColumn::make('open_tasks_count')
                    ->label('نا بشپړ کارونه')
                    ->state(fn (Meeting $record): int => $record->tasks()->where('status', '!=', TaskStatus::Done->value)->count()),
                IconColumn::make('is_confidential')
                    ->label('محرم')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('وروستی بدلون')
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->label('د نېټې فلټر')
                    ->form([
                        DatePicker::make('from')->label('له نېټې'),
                        DatePicker::make('to')->label('تر نېټې'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            filled($data['from'] ?? null),
                            fn (Builder $query): Builder => $query->whereDate('date', '>=', $data['from']),
                        )
                        ->when(
                            filled($data['to'] ?? null),
                            fn (Builder $query): Builder => $query->whereDate('date', '<=', $data['to']),
                        )),
                SelectFilter::make('is_confidential')
                    ->label('محرمیت')
                    ->options([
                        '1' => 'محرم',
                        '0' => 'غیر محرم',
                    ]),
                Filter::make('has_open_tasks')
                    ->label('نا بشپړ کارونه لري')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            'tasks',
                            fn (Builder $taskQuery): Builder => $taskQuery->where('status', '!=', TaskStatus::Done->value),
                        ),
                    ),
            ])
            ->recordActions([
                ViewAction::make()->label('کتنه'),
                EditAction::make()->label('سمون'),
                Action::make('decisions_pdf')
                    ->label('د پرېکړو PDF')
                    ->url(fn (Meeting $record): string => route('meetings.pdf.decisions', $record))
                    ->openUrlInNewTab(),
                Action::make('followup_pdf')
                    ->label('د تعقيب PDF')
                    ->url(fn (Meeting $record): string => route('meetings.pdf.followup', $record))
                    ->openUrlInNewTab(),
                Action::make('share')
                    ->label('شریکول')
                    ->url(fn (Meeting $record): string => route('meetings.share.create', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('ډله‌ییز ړنګول'),
                ]),
            ]);
    }
}
