<!doctype html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بشپړ مجلس — {{ $meeting->meeting_no }}</title>
    @include('pdf.partials.fonts-bahij')
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Bahij Nassim', "Noto Naskh Arabic", "DejaVu Sans", sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 2rem 2.5rem 2.5rem;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.75;
        }

        .header {
            border-bottom: 2px solid #0ea5e9;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .doc-title {
            margin: 0 0 0.25rem;
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            font-family: 'Bahij Nassim', "Noto Naskh Arabic", sans-serif;
        }

        .meeting-title {
            margin: 0;
            font-size: 15px;
            color: #64748b;
            font-weight: 500;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.4rem 2.5rem;
            margin-top: 1rem;
            font-size: 12px;
            color: #475569;
        }

        .meta strong {
            margin-left: 0.35rem;
            color: #334155;
        }

        .section-heading {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 1.25rem 0 0.6rem;
            font-family: 'Bahij Nassim', "Noto Naskh Arabic", sans-serif;
        }

        .prose-block {
            white-space: pre-wrap;
            margin: 0 0 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }

        .list-table .col-num {
            width: 3rem;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0.65rem;
            vertical-align: top;
        }

        th {
            background: #f1f5f9;
            font-weight: 700;
            font-size: 12px;
            color: #334155;
        }

        .empty-hint {
            margin: 0 0 1rem;
            color: #64748b;
            font-size: 12px;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 2rem;
            padding-top: 1rem;
            font-size: 11px;
            color: #94a3b8;
        }

        .confidential {
            color: #b91c1c;
            font-weight: 700;
        }
    </style>
</head>
<body>
@php
    $toLines = fn (string $text): \Illuminate\Support\Collection => collect(preg_split('/\r\n|\r|\n/', $text))
        ->map(fn (string $line): string => trim($line))
        ->filter()
        ->values();
    $agendaLines = $toLines((string) ($meeting->agenda ?? ''));
    $decisionLines = $toLines((string) $meeting->decisions_text);
    $followupLines = $toLines((string) ($meeting->followup_text ?? ''));
    $presentCount = $meeting->attendees->where('status', \App\Enums\AttendanceStatus::Present)->count();
    $absentCount = $meeting->attendees->where('status', \App\Enums\AttendanceStatus::Absent)->count();
@endphp

<div class="header">
    <h1 class="doc-title">بشپړ مجلس</h1>
    <p class="meeting-title">{{ $meeting->title }}</p>
    <div class="meta">
        <div><strong>شمېره:</strong> {{ $meeting->meeting_no }}</div>
        <div><strong>نېټه:</strong> {{ shamsi_date($meeting->date) }}</div>
        <div><strong>وخت:</strong> {{ $meeting->time ?? '—' }}</div>
        <div><strong>ځای:</strong> {{ $meeting->location ?? '—' }}</div>
        <div><strong>ټول ګډونوال:</strong> {{ $meeting->attendees->count() }}</div>
        <div><strong>حاضر:</strong> {{ $presentCount }} | <strong>غیر حاضر:</strong> {{ $absentCount }}</div>
    </div>
</div>

<p class="section-heading">اجنډا</p>
@if($agendaLines->isNotEmpty())
    <table class="list-table">
        <thead>
            <tr><th class="col-num">شمېره</th><th>مطلب</th></tr>
        </thead>
        <tbody>
            @foreach($agendaLines as $i => $line)
                <tr><td class="col-num">{{ $i + 1 }}</td><td>{{ $line }}</td></tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="prose-block">{{ $meeting->agenda ?? '—' }}</div>
@endif

<p class="section-heading">نوټونه</p>
<div class="prose-block">{{ $meeting->notes ?? '—' }}</div>

<p class="section-heading">پرېکړې</p>
@if($decisionLines->isNotEmpty())
    <table class="list-table">
        <thead>
            <tr><th class="col-num">شمېره</th><th>پرېکړه</th></tr>
        </thead>
        <tbody>
            @foreach($decisionLines as $i => $line)
                <tr><td class="col-num">{{ $i + 1 }}</td><td>{{ $line }}</td></tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="empty-hint">پرېکړه ثبت شوې نه ده.</p>
@endif

@if($followupLines->isNotEmpty())
    <p class="section-heading">تعقيب</p>
    <table class="list-table">
        <thead>
            <tr><th class="col-num">شمېره</th><th>ګام / مطلب</th></tr>
        </thead>
        <tbody>
            @foreach($followupLines as $i => $line)
                <tr><td class="col-num">{{ $i + 1 }}</td><td>{{ $line }}</td></tr>
            @endforeach
        </tbody>
    </table>
@elseif(filled($meeting->followup_text))
    <p class="section-heading">تعقيب</p>
    <div class="prose-block">{{ $meeting->followup_text }}</div>
@endif

<p class="section-heading">د مجلس غړي</p>
<table>
    <thead>
    <tr>
        <th>نوم</th>
        <th>حالت</th>
        <th>علت</th>
    </tr>
    </thead>
    <tbody>
    @forelse($meeting->attendees as $attendee)
        <tr>
            <td>{{ $attendee->display_name }}</td>
            <td>
                @if($attendee->status instanceof \App\Enums\AttendanceStatus)
                    {{ $attendee->status->label() }}
                @else
                    {{ $attendee->status }}
                @endif
            </td>
            <td>{{ $attendee->reason ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3">هیڅ ګډونوال نشته.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<p class="section-heading">کارونه / دندې</p>
<table>
    <thead>
    <tr>
        <th>شمېره</th>
        <th>موضوع / دنده</th>
        <th>مسؤل</th>
        <th>وروستۍ نېټه</th>
        <th>حالت</th>
        <th>یادښت</th>
    </tr>
    </thead>
    <tbody>
    @forelse($meeting->tasks as $task)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $task->title }}</td>
            <td>{{ $task->owner ?? '—' }}</td>
            <td>{{ shamsi_date($task->due_date) }}</td>
            <td>
                @if($task->status instanceof \App\Enums\TaskStatus)
                    {{ $task->status->label() }}
                @else
                    {{ $task->status }}
                @endif
            </td>
            <td>{{ $task->description ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">هیڅ دنده نشته.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    @if($meeting->is_confidential)
        <span class="confidential">دا سند محرم دی، د غیر مجاز شریکولو اجازه نشته.</span>
    @else
        بشپړ مجلس — شمېره {{ $meeting->meeting_no }} — {{ shamsi_date($meeting->date) }}
    @endif
</div>
</body>
</html>
