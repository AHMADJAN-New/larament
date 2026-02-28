<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AttendanceStatus;
use App\Filament\Resources\Meetings\MeetingResource;
use App\Models\Meeting;
use DateTimeInterface;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

final class RecentMeetingsAttendanceTableWidget extends TableWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'وروستې غونډې – حاضري';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Meeting::query()
                    ->withCount([
                        'attendees',
                        'attendees as present_count' => fn (Builder $query): Builder => $query->where('status', AttendanceStatus::Present->value),
                        'attendees as absent_count' => fn (Builder $query): Builder => $query->where('status', AttendanceStatus::Absent->value),
                    ])
                    ->orderByDesc('date')
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('meeting_no')
                    ->label('شمېره')
                    ->alignment(Alignment::Center),
                TextColumn::make('title')
                    ->label('عنوان')
                    ->alignment(Alignment::Center)
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('date')
                    ->label('نېټه')
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn (\Carbon\Carbon|DateTimeInterface|string|null $state): string => shamsi_date($state))
                    ->sortable(),
                TextColumn::make('present_count')
                    ->label('حاضر')
                    ->alignment(Alignment::Center)
                    ->color('success'),
                TextColumn::make('absent_count')
                    ->label('غیر حاضر')
                    ->alignment(Alignment::Center)
                    ->color('danger'),
                TextColumn::make('attendees_count')
                    ->label('ټول')
                    ->alignment(Alignment::Center),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('کتنه')
                    ->url(fn (Meeting $record): string => MeetingResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
