<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\MeetingResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateMeeting extends CreateRecord
{
    protected static string $resource = MeetingResource::class;
}
