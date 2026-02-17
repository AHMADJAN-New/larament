<!doctype html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>د مجلس پرېکړې</title>
    <style>
        body {
            font-family: "Noto Naskh Arabic", "DejaVu Sans", sans-serif;
            direction: rtl;
            text-align: right;
            margin: 2rem;
            color: #111827;
            font-size: 14px;
            line-height: 1.7;
        }

        .header {
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        h1 {
            margin: 0 0 .5rem;
            font-size: 26px;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .35rem 2rem;
            margin-top: 1rem;
        }

        .meta strong {
            margin-left: .35rem;
        }

        ol {
            padding-right: 1.2rem;
        }

        li {
            margin-bottom: .5rem;
        }

        .footer {
            border-top: 1px solid #d1d5db;
            margin-top: 1.5rem;
            padding-top: .8rem;
            color: #b91c1c;
            font-weight: 700;
        }
    </style>
</head>
<body>
@php
    $decisionLines = collect(preg_split('/\r\n|\r|\n/', (string) $meeting->decisions_text))
        ->map(fn (string $line): string => trim($line))
        ->filter()
        ->values();

    $presentCount = $meeting->attendees->where('status', \App\Enums\AttendanceStatus::Present)->count();
    $absentCount = $meeting->attendees->where('status', \App\Enums\AttendanceStatus::Absent)->count();
@endphp

<div class="header">
    <h1>د مجلس پرېکړې</h1>
    <div class="meta">
        <div><strong>شمېره:</strong> {{ $meeting->meeting_no }}</div>
        <div><strong>نېټه:</strong> {{ $meeting->date?->format('Y-m-d') }}</div>
        <div><strong>وخت:</strong> {{ $meeting->time ?? '-' }}</div>
        <div><strong>ځای:</strong> {{ $meeting->location ?? '-' }}</div>
        <div><strong>ټول ګډونوال:</strong> {{ $meeting->attendees->count() }}</div>
        <div><strong>حاضر:</strong> {{ $presentCount }} | <strong>غیر حاضر:</strong> {{ $absentCount }}</div>
    </div>
</div>

<ol>
    @forelse($decisionLines as $line)
        <li>{{ $line }}</li>
    @empty
        <li>پرېکړه ثبت شوې نه ده.</li>
    @endforelse
</ol>

@if($meeting->is_confidential)
    <div class="footer">دا سند محرم دی، د غیر مجاز شریکولو اجازه نشته.</div>
@endif
</body>
</html>
