<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نوم')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('برېښنالیک')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('رول')
                    ->badge()
                    ->formatStateUsing(function (UserRole|string $state): string {
                        $role = $state instanceof UserRole ? $state : UserRole::from($state);

                        return $role->label();
                    })
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('د برېښنالیک تایید')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('جوړ شوی')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('بدل شوی')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
