<!doctype html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعقيب</title>
    <style>
        body {
            font-family: "Noto Naskh Arabic", "DejaVu Sans", sans-serif;
            direction: rtl;
            text-align: right;
            margin: 2rem;
            color: #111827;
            font-size: 13px;
            line-height: 1.7;
        }

        h1 {
            margin: 0 0 .8rem;
            font-size: 26px;
        }

        .meta {
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: .45rem .55rem;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
        }

        .summary {
            margin-top: 1rem;
            padding: .75rem;
            border: 1px dashed #9ca3af;
            background: #fafafa;
        }
    </style>
</head>
<body>
<h1>د تعقيب راپور</h1>
<div class="meta">
    <strong>شمېره:</strong> {{ $meeting->meeting_no }} |
    <strong>عنوان:</strong> {{ $meeting->title }} |
    <strong>نېټه:</strong> {{ $meeting->date?->format('Y-m-d') }}
</div>

@if(filled($meeting->followup_text))
    <div class="summary">
        <strong>عمومي تعقيب:</strong><br>
        {!! nl2br(e($meeting->followup_text)) !!}
    </div>
@endif

<table>
    <thead>
    <tr>
        <th>شمېره</th>
        <th>موضوع/دنده</th>
        <th>مسؤل</th>
        <th>Deadline</th>
        <th>حالت</th>
        <th>یادښت</th>
    </tr>
    </thead>
    <tbody>
    @forelse($meeting->tasks as $task)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $task->title }}</td>
            <td>{{ $task->owner ?? '-' }}</td>
            <td>{{ $task->due_date?->format('Y-m-d') ?? '-' }}</td>
            <td>
                @if($task->status instanceof \App\Enums\TaskStatus)
                    {{ $task->status->label() }}
                @else
                    {{ $task->status }}
                @endif
            </td>
            <td>{{ $task->description ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">هیڅ دنده نشته.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
