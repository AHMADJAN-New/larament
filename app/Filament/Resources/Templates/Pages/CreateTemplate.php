<?php

declare(strict_types=1);

namespace App\Filament\Resources\Templates\Pages;

use App\Filament\Resources\Templates\TemplateResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateTemplate extends CreateRecord
{
    protected static string $resource = TemplateResource::class;
}
