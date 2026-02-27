<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\MeetingResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListMeetings extends ListRecords
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calendar')
                ->label('کلینډر')
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn (): string => MeetingResource::getUrl('calendar')),
            CreateAction::make()->label('نوی مجلس'),
        ];
    }
}
