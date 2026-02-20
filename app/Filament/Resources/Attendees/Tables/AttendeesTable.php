<?php

declare(strict_types=1);

namespace App\Filament\Resources\Attendees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AttendeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نوم')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('کاروونکی')
                    ->alignment(Alignment::Center)
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('وروستی بدلون')
                    ->alignment(Alignment::Center)
                    ->since(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make()->label('کتنه')->slideOver(),
                EditAction::make()->label('سمون')->slideOver(),
                DeleteAction::make()->label('ړنګول'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('ډله‌ییز ړنګول'),
                ]),
            ]);
    }
}
