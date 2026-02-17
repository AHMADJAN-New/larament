<!doctype html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د مجلس شریکول</title>
    <style>
        body {
            font-family: "Noto Naskh Arabic", "Segoe UI", sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            background: #f8fafc;
            color: #111827;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .title {
            margin: 0 0 .75rem;
            font-size: 1.6rem;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem 1rem;
            color: #374151;
            margin-bottom: .5rem;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .75rem;
        }

        .btn {
            text-decoration: none;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #111827;
            padding: .5rem .8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: .95rem;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        textarea {
            width: 100%;
            min-height: 170px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: .6rem;
            font-family: inherit;
            resize: vertical;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: .45rem .55rem;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1 class="title">د مجلس شریکول</h1>
        <div class="meta">
            <span><strong>شمېره:</strong> {{ $meeting->meeting_no }}</span>
            <span><strong>عنوان:</strong> {{ $meeting->title }}</span>
            <span><strong>نېټه:</strong> {{ $meeting->date?->format('Y-m-d') }}</span>
            <span><strong>ځای:</strong> {{ $meeting->location ?? '-' }}</span>
        </div>
        @if($meeting->is_confidential)
            <div><strong style="color:#b91c1c;">دا مجلس محرم دی.</strong></div>
        @endif
    </div>

    <div class="card">
        <form method="GET" action="{{ route('meetings.share.show', $shareLink->token) }}">
            <label for="variant"><strong>د WhatsApp پیغام ډول:</strong></label>
            <select class="btn" id="variant" name="variant" onchange="this.form.submit()">
                <option value="short" @selected($variant === 'short')>لنډیز (Short)</option>
                <option value="detailed" @selected($variant === 'detailed')>تفصیلي (Detailed)</option>
                <option value="tasks_only" @selected($variant === 'tasks_only')>یوازې کارونه</option>
                <option value="absent_only" @selected($variant === 'absent_only')>یوازې غیر حاضر</option>
                <option value="full" @selected($variant === 'full')>بشپړ مجلس</option>
            </select>
        </form>

        <div style="margin-top:.8rem;">
            <textarea id="share-text" readonly>{{ $message }}</textarea>
        </div>

        <div class="buttons">
            <button id="copy-button" class="btn" type="button">Copy</button>
            <a class="btn btn-primary" href="{{ $whatsAppUrl }}" target="_blank" rel="noopener">Open WhatsApp</a>
            <a class="btn" href="{{ route('meetings.share.pdf.decisions', $shareLink->token) }}" target="_blank" rel="noopener">
                Decisions PDF
            </a>
            <a class="btn" href="{{ route('meetings.share.pdf.followup', $shareLink->token) }}" target="_blank" rel="noopener">
                Follow-up PDF
            </a>
        </div>
    </div>

    <div class="card">
        <h2 style="margin-top:0;">کارونه</h2>
        <table>
            <thead>
            <tr>
                <th>شمېره</th>
                <th>موضوع</th>
                <th>مسؤل</th>
                <th>وروستۍ نېټه</th>
                <th>حالت</th>
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
                </tr>
            @empty
                <tr>
                    <td colspan="5">هیڅ دنده نشته.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    const copyButton = document.getElementById('copy-button');
    const shareText = document.getElementById('share-text');

    copyButton.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(shareText.value);
            copyButton.textContent = 'Copied';
        } catch (e) {
            shareText.select();
            document.execCommand('copy');
            copyButton.textContent = 'Copied';
        }
    });
</script>
</body>
</html>
