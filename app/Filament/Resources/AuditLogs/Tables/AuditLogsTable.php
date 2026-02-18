<?php

declare(strict_types=1);

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('وخت')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('actor.name')
                    ->label('کاروونکی')
                    ->default('-')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('عمل')
                    ->badge(),
                TextColumn::make('entity_type')
                    ->label('ډول')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('entity_id')
                    ->label('ID'),
                TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
