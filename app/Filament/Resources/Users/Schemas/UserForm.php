<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نوم')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('email')
                    ->label('برېښنالیک')
                    ->maxLength(255)
                    ->unique()
                    ->email()
                    ->required(),
                Select::make('role')
                    ->label('رول')
                    ->options(UserRole::options())
                    ->default(UserRole::Member->value)
                    ->required(),
                TextInput::make('password')
                    ->label('پټنوم')
                    ->password()
                    ->required(fn ($livewire): bool => $livewire instanceof CreateUser)
                    ->revealable(filament()->arePasswordsRevealable())
                    ->rule(Password::default())
                    ->autocomplete('new-password')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->dehydrateStateUsing(fn ($state): string => Hash::make($state)),
            ]);
    }
}
