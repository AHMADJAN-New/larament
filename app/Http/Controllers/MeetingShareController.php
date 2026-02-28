<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\ShareLink;
use App\Services\MeetingShareMessageService;
use App\Services\PdfService;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

final class MeetingShareController extends Controller
{
    public function __construct(
        private readonly PdfService $pdfService,
        private readonly MeetingShareMessageService $meetingShareMessageService,
    ) {}

    public function create(Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        /** @var ShareLink $shareLink */
        $shareLink = $meeting->shareLinks()->create([
            'expires_at' => now()->addDays(7),
        ]);

        if (Filament::auth()->check()) {
            return redirect()->to(Filament::getUrl().'/share-meeting?token='.urlencode((string) $shareLink->token));
        }

        return redirect()->route('meetings.share.show', $shareLink->token);
    }

    public function show(Request $request, string $token): View
    {
        $shareLink = $this->resolveShareLink($token);
        /** @var Meeting $meeting */
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);

        $variant = (string) $request->string('variant', 'short');
        $variant = in_array($variant, $this->variants(), true) ? $variant : 'short';

        $message = $this->meetingShareMessageService->build($meeting, $variant);

        return view('share.meeting', [
            'meeting' => $meeting,
            'shareLink' => $shareLink,
            'variant' => $variant,
            'variants' => $this->variants(),
            'message' => $message,
            'whatsAppUrl' => 'https://wa.me/?text='.rawurlencode($message),
        ]);
    }

    public function decisions(string $token): Response|RedirectResponse
    {
        $shareLink = $this->resolveShareLink($token);
        /** @var Meeting $meeting */
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);

        try {
            return $this->pdfService->streamFromView(
                view: 'pdf.meeting-decisions',
                data: ['meeting' => $meeting],
                downloadName: 'meeting-'.$meeting->meeting_no.'-decisions.pdf',
            );
        } catch (Throwable) {
            return redirect()->route('meetings.share.show', $token)
                ->with('error', __('PDF generation failed. Please try again later.'));
        }
    }

    public function followup(string $token): Response|RedirectResponse
    {
        $shareLink = $this->resolveShareLink($token);
        /** @var Meeting $meeting */
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);

        try {
            return $this->pdfService->streamFromView(
                view: 'pdf.meeting-followup',
                data: ['meeting' => $meeting],
                downloadName: 'meeting-'.$meeting->meeting_no.'-followup.pdf',
            );
        } catch (Throwable) {
            return redirect()->route('meetings.share.show', $token)
                ->with('error', __('PDF generation failed. Please try again later.'));
        }
    }

    public function complete(string $token): Response|RedirectResponse
    {
        $shareLink = $this->resolveShareLink($token);
        /** @var Meeting $meeting */
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);

        try {
            return $this->pdfService->streamFromView(
                view: 'pdf.meeting-complete',
                data: ['meeting' => $meeting],
                downloadName: 'meeting-'.$meeting->meeting_no.'-complete.pdf',
            );
        } catch (Throwable) {
            return redirect()->route('meetings.share.show', $token)
                ->with('error', __('PDF generation failed. Please try again later.'));
        }
    }

    private function resolveShareLink(string $token): ShareLink
    {
        $shareLink = ShareLink::query()
            ->where('token', $token)
            ->with('meeting')
            ->firstOrFail();

        abort_if($shareLink->isExpired(), 410);

        return $shareLink;
    }

    /**
     * @return list<string>
     */
    private function variants(): array
    {
        return [
            'short',
            'detailed',
            'tasks_only',
            'absent_only',
            'full',
        ];
    }
}
