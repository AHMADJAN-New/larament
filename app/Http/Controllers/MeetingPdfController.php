<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Services\ChromePdfService;
use Illuminate\Http\Response;

final class MeetingPdfController extends Controller
{
    public function __construct(private readonly ChromePdfService $chromePdfService) {}

    public function decisions(Meeting $meeting): Response
    {
        $this->authorize('view', $meeting);

        $meeting->load(['attendees', 'tasks']);

        return $this->chromePdfService->streamFromView(
            view: 'pdf.meeting-decisions',
            data: ['meeting' => $meeting],
            downloadName: 'meeting-'.$meeting->meeting_no.'-decisions.pdf',
        );
    }

    public function followup(Meeting $meeting): Response
    {
        $this->authorize('view', $meeting);

        $meeting->load(['attendees', 'tasks']);

        return $this->chromePdfService->streamFromView(
            view: 'pdf.meeting-followup',
            data: ['meeting' => $meeting],
            downloadName: 'meeting-'.$meeting->meeting_no.'-followup.pdf',
        );
    }

    public function complete(Meeting $meeting): Response
    {
        $this->authorize('view', $meeting);

        $meeting->load(['attendees', 'tasks']);

        return $this->chromePdfService->streamFromView(
            view: 'pdf.meeting-complete',
            data: ['meeting' => $meeting],
            downloadName: 'meeting-'.$meeting->meeting_no.'-complete.pdf',
        );
    }
}
