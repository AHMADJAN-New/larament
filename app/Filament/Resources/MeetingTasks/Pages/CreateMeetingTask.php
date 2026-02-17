<?php

declare(strict_types=1);

namespace App\Filament\Resources\MeetingTasks\Pages;

use App\Filament\Resources\MeetingTasks\MeetingTaskResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateMeetingTask extends CreateRecord
{
    protected static string $resource = MeetingTaskResource::class;
}
