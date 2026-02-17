<?php

declare(strict_types=1);

namespace App\Filament\Resources\Templates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class TemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نوم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('meeting_title_default')
                    ->label('د مجلس عنوان')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('وروستی بدلون')
                    ->since(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()->label('سمون'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('ډله‌ییز ړنګول'),
                ]),
            ]);
    }
}
