<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\MeetingResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditMeeting extends EditRecord
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('decisionsPdf')
                ->label('د پرېکړو PDF')
                ->url(fn (): string => route('meetings.pdf.decisions', $this->record))
                ->openUrlInNewTab(),
            Action::make('followupPdf')
                ->label('د تعقيب PDF')
                ->url(fn (): string => route('meetings.pdf.followup', $this->record))
                ->openUrlInNewTab(),
            Action::make('shareLink')
                ->label('د شریکولو لینک')
                ->url(fn (): string => route('meetings.share.create', $this->record))
                ->openUrlInNewTab(),
            DeleteAction::make()->label('ړنګول'),
        ];
    }
}
