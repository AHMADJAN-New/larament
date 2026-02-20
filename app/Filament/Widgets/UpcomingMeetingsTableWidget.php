<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Filament\Resources\Meetings\MeetingResource;
use App\Models\Meeting;
use DateTimeInterface;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

final class UpcomingMeetingsTableWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'راتلونکې غونډې';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Meeting::query()
                    ->where('date', '>=', now()->toDateString())
                    ->orderBy('date')
                    ->orderBy('time')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('meeting_no')
                    ->label('شمېره')
                    ->alignment(Alignment::Center)
                    ->sortable(),
                TextColumn::make('title')
                    ->label('عنوان')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->limit(35),
                TextColumn::make('date')
                    ->label('نېټه')
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn (\Carbon\Carbon|DateTimeInterface|string|null $state): string => shamsi_date($state))
                    ->sortable(),
                TextColumn::make('time')
                    ->label('وخت')
                    ->alignment(Alignment::Center)
                    ->time(),
                TextColumn::make('location')
                    ->label('ځای')
                    ->alignment(Alignment::Center)
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attendees_count')
                    ->label('غړي')
                    ->alignment(Alignment::Center)
                    ->counts('attendees'),
                TextColumn::make('open_tasks')
                    ->label('نا بشپړ کارونه')
                    ->alignment(Alignment::Center)
                    ->state(fn (Meeting $record): int => $record->tasks()->where('status', '!=', TaskStatus::Done->value)->count()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('کتنه')
                    ->url(fn (Meeting $record): string => MeetingResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
