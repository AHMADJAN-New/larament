<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Meeting;
use App\Models\ShareLink;
use App\Services\MeetingShareMessageService;
use Filament\Facades\Filament;
use Filament\Pages\Page;

final class ShareMeetingPage extends Page
{
    public ?Meeting $meeting = null;

    public ?ShareLink $shareLink = null;

    public string $variant = 'short';

    public string $message = '';

    public string $whatsAppUrl = '';

    /**
     * @var list<string>
     */
    public array $variants = [];

    protected static ?string $slug = 'share-meeting';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        $token = request()->query('token');
        if (blank($token)) {
            $this->redirect(Filament::getUrl());

            return;
        }

        $shareLink = ShareLink::query()
            ->where('token', $token)
            ->with('meeting')
            ->first();

        if (! $shareLink || $shareLink->isExpired()) {
            abort(404);
        }

        $this->shareLink = $shareLink;
        /** @var Meeting $meeting */
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);
        $this->meeting = $meeting;

        $variant = (string) request()->string('variant', 'short');
        $variants = ['short', 'detailed', 'tasks_only', 'absent_only', 'full'];
        $this->variant = in_array($variant, $variants, true) ? $variant : 'short';
        $this->variants = $variants;

        $this->message = app(MeetingShareMessageService::class)->build($meeting, $this->variant);
        $this->whatsAppUrl = 'https://wa.me/?text='.rawurlencode($this->message);
    }

    public function getTitle(): string
    {
        return $this->meeting instanceof \App\Models\Meeting
            ? 'د مجلس شریکول — '.$this->meeting->title
            : 'د مجلس شریکول';
    }

    public function getHeading(): string
    {
        return 'د مجلس شریکول';
    }

    public function getView(): string
    {
        return 'filament.pages.share-meeting';
    }
}
