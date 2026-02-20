<?php

declare(strict_types=1);

namespace App\Filament\Resources\AuditLogs\Tables;

use DateTimeInterface;
use Filament\Support\Enums\Alignment;
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
                    ->alignment(Alignment::Center)
                    ->formatStateUsing(fn (\Carbon\Carbon|DateTimeInterface|string|null $state): string => shamsi_datetime($state))
                    ->sortable(),
                TextColumn::make('actor.name')
                    ->label('کاروونکی')
                    ->alignment(Alignment::Center)
                    ->default('-')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('عمل')
                    ->alignment(Alignment::Center)
                    ->badge(),
                TextColumn::make('entity_type')
                    ->label('ډول')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('entity_id')
                    ->label('ID')
                    ->alignment(Alignment::Center),
                TextColumn::make('ip')
                    ->label('IP')
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
