<?php

declare(strict_types=1);

namespace App\Filament\Resources\Meetings\Pages;

use App\Filament\Resources\Meetings\MeetingResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

final class EditMeeting extends EditRecord
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('completePdf')
                    ->label('بشپړ PDF')
                    ->url(fn (): string => route('meetings.pdf.complete', $this->record))
                    ->openUrlInNewTab()
                    ->icon(Heroicon::OutlinedDocumentText),
                Action::make('decisionsPdf')
                    ->label('د پرېکړو PDF')
                    ->url(fn (): string => route('meetings.pdf.decisions', $this->record))
                    ->openUrlInNewTab()
                    ->icon(Heroicon::OutlinedDocumentCheck),
                Action::make('followupPdf')
                    ->label('د تعقيب PDF')
                    ->url(fn (): string => route('meetings.pdf.followup', $this->record))
                    ->openUrlInNewTab()
                    ->icon(Heroicon::OutlinedClipboardDocumentList),
            ])
                ->label('PDF')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('gray')
                ->button(),
            Action::make('shareLink')
                ->label('د شریکولو لینک')
                ->url(fn (): string => route('meetings.share.create', $this->record))
                ->icon(Heroicon::OutlinedShare)
                ->color('success'),
            DeleteAction::make()->label('ړنګول'),
        ];
    }
}
