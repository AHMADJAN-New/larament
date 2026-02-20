<?php

declare(strict_types=1);

namespace App\Filament\Resources\Attendees\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class AttendeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نوم')
                    ->required()
                    ->maxLength(255),
                Select::make('user_id')
                    ->label('کاروونکی')
                    ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
            ]);
    }
}
