<!doctype html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>د تعقيب راپور — {{ $meeting->meeting_no }}</title>
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
            margin-top: 0.75rem;
            font-size: 12px;
            color: #475569;
        }

        .meta strong {
            margin-left: 0.35rem;
            color: #334155;
        }

        .summary {
            margin: 1.25rem 0 1.5rem;
            padding: 1rem 1.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .summary-heading {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.5rem;
        }

        .section-heading {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 1.25rem 0 0.6rem;
            font-family: 'Bahij Nassim', "Noto Naskh Arabic", sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
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

        .list-table .col-num {
            width: 3rem;
            text-align: center;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 2rem;
            padding-top: 1rem;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="header">
    <h1 class="doc-title">د تعقيب راپور</h1>
    <p class="meeting-title">{{ $meeting->title }}</p>
    <div class="meta">
        <strong>شمېره:</strong> {{ $meeting->meeting_no }} ·
        <strong>نېټه:</strong> {{ shamsi_date($meeting->date) }} ·
        <strong>وخت:</strong> {{ $meeting->time ?? '—' }} ·
        <strong>ځای:</strong> {{ $meeting->location ?? '—' }}
    </div>
</div>

@php
    $followupLines = collect(preg_split('/\r\n|\r|\n/', (string) ($meeting->followup_text ?? '')))
        ->map(fn (string $line): string => trim($line))
        ->filter()
        ->values();
@endphp
@if($followupLines->isNotEmpty())
    <p class="section-heading">عمومي تعقيب</p>
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
    <div class="summary">
        <p class="summary-heading">عمومي تعقيب</p>
        {!! nl2br(e($meeting->followup_text)) !!}
    </div>
@endif

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
    د تعقيب راپور — شمېره {{ $meeting->meeting_no }} — {{ shamsi_date($meeting->date) }}
</div>
</body>
</html>
