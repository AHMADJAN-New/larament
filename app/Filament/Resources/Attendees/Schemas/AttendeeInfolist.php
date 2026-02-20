<?php

declare(strict_types=1);

namespace App\Filament\Resources\Attendees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AttendeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('د مجلس غړی')
                    ->schema([
                        TextEntry::make('name')
                            ->label('نوم'),
                        TextEntry::make('user.name')
                            ->label('کاروونکی')
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
