<?php

declare(strict_types=1);

namespace App\Filament\Resources\MeetingTasks;

use App\Filament\Resources\MeetingTasks\Pages\CreateMeetingTask;
use App\Filament\Resources\MeetingTasks\Pages\EditMeetingTask;
use App\Filament\Resources\MeetingTasks\Pages\ListMeetingTasks;
use App\Filament\Resources\MeetingTasks\Schemas\MeetingTaskForm;
use App\Filament\Resources\MeetingTasks\Tables\MeetingTasksTable;
use App\Models\MeetingTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class MeetingTaskResource extends Resource
{
    protected static ?string $model = MeetingTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return 'مجلسونه';
    }

    public static function getNavigationLabel(): string
    {
        return 'کارونه';
    }

    public static function getModelLabel(): string
    {
        return 'دنده';
    }

    public static function getPluralModelLabel(): string
    {
        return 'کارونه';
    }

    public static function form(Schema $schema): Schema
    {
        return MeetingTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeetingTasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMeetingTasks::route('/'),
            'create' => CreateMeetingTask::route('/create'),
            'edit' => EditMeetingTask::route('/{record}/edit'),
        ];
    }
}
