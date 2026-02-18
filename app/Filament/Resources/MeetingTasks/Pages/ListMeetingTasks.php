<?php

declare(strict_types=1);

namespace App\Filament\Resources\MeetingTasks\Pages;

use App\Filament\Resources\MeetingTasks\MeetingTaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListMeetingTasks extends ListRecords
{
    protected static string $resource = MeetingTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('نوی کار'),
        ];
    }
}
