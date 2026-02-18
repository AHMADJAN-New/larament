<?php

declare(strict_types=1);

namespace App\Filament\Resources\Templates\Schemas;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class TemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('د نمونې نوم')
                    ->required()
                    ->maxLength(255),
                TextInput::make('meeting_title_default')
                    ->label('د مجلس پیش‌فرض عنوان')
                    ->maxLength(255),
                Textarea::make('agenda_template')
                    ->label('د اجنډا نمونه')
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('notes_template')
                    ->label('د نوټونو نمونه')
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('decisions_template')
                    ->label('د پرېکړو نمونه')
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('followup_template')
                    ->label('د تعقيب نمونه')
                    ->rows(4)
                    ->columnSpanFull(),
                Repeater::make('tasks_template')
                    ->label('پیش‌فرض کارونه')
                    ->defaultItems(0)
                    ->addActionLabel('دنده زیات کړئ')
                    ->schema([
                        TextInput::make('title')
                            ->label('موضوع/دنده')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('owner')
                            ->label('مسؤل')
                            ->maxLength(255),
                        DatePicker::make('due_date')
                            ->label('وروستۍ نېټه'),
                        Select::make('priority')
                            ->label('اولویت')
                            ->options(TaskPriority::options())
                            ->default(TaskPriority::Medium->value)
                            ->required(),
                        Select::make('status')
                            ->label('حالت')
                            ->options(TaskStatus::options())
                            ->default(TaskStatus::Open->value)
                            ->required(),
                        Textarea::make('description')
                            ->label('تشریح')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
