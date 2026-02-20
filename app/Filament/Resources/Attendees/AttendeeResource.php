<?php

declare(strict_types=1);

namespace App\Filament\Resources\Attendees;

use App\Filament\Resources\Attendees\Pages\ListAttendees;
use App\Filament\Resources\Attendees\Schemas\AttendeeForm;
use App\Filament\Resources\Attendees\Schemas\AttendeeInfolist;
use App\Filament\Resources\Attendees\Tables\AttendeesTable;
use App\Models\Attendee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class AttendeeResource extends Resource
{
    protected static ?string $model = Attendee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return 'مجلسونه';
    }

    public static function getNavigationLabel(): string
    {
        return 'د مجلس غړي';
    }

    public static function getModelLabel(): string
    {
        return 'غړی';
    }

    public static function getPluralModelLabel(): string
    {
        return 'د مجلس غړي';
    }

    public static function form(Schema $schema): Schema
    {
        return AttendeeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendeeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendees::route('/'),
        ];
    }
}
