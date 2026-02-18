<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Schemas;

use App\Enums\AttendanceStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class MeetingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('د مجلس معلومات')
                    ->schema([
                        TextEntry::make('meeting_no')
                            ->label('د مجلس شمېره'),
                        TextEntry::make('title')
                            ->label('عنوان')
                            ->weight('bold'),
                        TextEntry::make('date')
                            ->label('نېټه')
                            ->date(),
                        TextEntry::make('time')
                            ->label('وخت')
                            ->time(),
                        TextEntry::make('location')
                            ->label('ځای'),
                        IconEntry::make('is_confidential')
                            ->label('محرم مجلس')
                            ->boolean()
                            ->trueIcon(Heroicon::LockClosed)
                            ->falseIcon(Heroicon::LockOpen),
                        TextEntry::make('agenda')
                            ->label('اجنډا')
                            ->columnSpanFull()
                            ->prose(),
                        TextEntry::make('notes')
                            ->label('نوټونه')
                            ->columnSpanFull()
                            ->prose(),
                        TextEntry::make('decisions_text')
                            ->label('پرېکړې')
                            ->columnSpanFull()
                            ->prose(),
                        TextEntry::make('followup_text')
                            ->label('تعقيب')
                            ->columnSpanFull()
                            ->prose(),
                    ])
                    ->columns(['default' => 1, 'md' => 2]),
                Section::make('حاضري')
                    ->schema([
                        RepeatableEntry::make('attendees')
                            ->label('ګډونوال')
                            ->table([
                                TableColumn::make('نوم'),
                                TableColumn::make('حالت'),
                                TableColumn::make('علت'),
                            ])
                            ->schema([
                                TextEntry::make('display_name')
                                    ->label('نوم')
                                    ->placeholder('—'),
                                TextEntry::make('status')
                                    ->label('حالت')
                                    ->formatStateUsing(fn ($state): string => $state instanceof AttendanceStatus ? $state->label() : (string) $state),
                                TextEntry::make('reason')
                                    ->label('علت')
                                    ->placeholder('—'),
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make('تعقيب / کارونه')
                    ->schema([
                        RepeatableEntry::make('tasks')
                            ->label('کارونه')
                            ->table([
                                TableColumn::make('موضوع/دنده'),
                                TableColumn::make('مسؤل'),
                                TableColumn::make('وروستۍ نېټه'),
                                TableColumn::make('اولویت'),
                                TableColumn::make('حالت'),
                            ])
                            ->schema([
                                TextEntry::make('title')
                                    ->label('موضوع/دنده'),
                                TextEntry::make('owner')
                                    ->label('مسؤل')
                                    ->placeholder('—'),
                                TextEntry::make('due_date')
                                    ->label('وروستۍ نېټه')
                                    ->date()
                                    ->placeholder('—'),
                                TextEntry::make('priority')
                                    ->label('اولویت')
                                    ->formatStateUsing(fn ($state) => $state?->label() ?? (string) $state),
                                TextEntry::make('status')
                                    ->label('حالت')
                                    ->formatStateUsing(fn ($state) => $state?->label() ?? (string) $state),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
