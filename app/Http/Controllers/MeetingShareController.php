<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\ShareLink;
use App\Services\ChromePdfService;
use App\Services\MeetingShareMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class MeetingShareController extends Controller
{
    public function __construct(
        private readonly ChromePdfService $chromePdfService,
        private readonly MeetingShareMessageService $meetingShareMessageService,
    ) {}

    public function create(Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $shareLink = $meeting->shareLinks()->create([
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('meetings.share.show', $shareLink->token);
    }

    public function show(Request $request, string $token): View
    {
        $shareLink = $this->resolveShareLink($token);
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);

        $variant = (string) $request->string('variant', 'short');
        $variant = in_array($variant, $this->variants(), true) ? $variant : 'short';

        $shareUrl = route('meetings.share.show', $shareLink->token);
        $message = $this->meetingShareMessageService->build($meeting, $variant, $shareUrl);

        return view('share.meeting', [
            'meeting' => $meeting,
            'shareLink' => $shareLink,
            'variant' => $variant,
            'variants' => $this->variants(),
            'message' => $message,
            'whatsAppUrl' => 'https://wa.me/?text='.rawurlencode($message),
        ]);
    }

    public function decisions(string $token): Response
    {
        $shareLink = $this->resolveShareLink($token);
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);

        return $this->chromePdfService->streamFromView(
            view: 'pdf.meeting-decisions',
            data: ['meeting' => $meeting],
            downloadName: 'meeting-'.$meeting->meeting_no.'-decisions.pdf',
        );
    }

    public function followup(string $token): Response
    {
        $shareLink = $this->resolveShareLink($token);
        $meeting = $shareLink->meeting->load(['attendees', 'tasks']);

        return $this->chromePdfService->streamFromView(
            view: 'pdf.meeting-followup',
            data: ['meeting' => $meeting],
            downloadName: 'meeting-'.$meeting->meeting_no.'-followup.pdf',
        );
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
