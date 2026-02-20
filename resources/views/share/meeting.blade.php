<!doctype html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د مجلس شریکول — {{ $meeting->title }}</title>
    <style>
        @font-face {
            font-family: 'Bahij Nassim';
            src: url('/fonts/Bahij Nassim-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Bahij Nassim';
            src: url('/fonts/Bahij Nassim-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        :root {
            --bg: #f0f4f8;
            --card: #fff;
            --border: #e2e8f0;
            --text: #1e293b;
            --text-muted: #64748b;
            --primary: #0ea5e9;
            --primary-hover: #0284c7;
            --whatsapp: #25d366;
            --whatsapp-hover: #20bd5a;
            --success: #059669;
            --error-bg: #fef2f2;
            --error-border: #b91c1c;
        }

        body {
            font-family: 'Bahij Nassim', "Noto Naskh Arabic", "Segoe UI", sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 56rem;
            margin: 0 auto;
            padding: 1.5rem 1rem 3rem;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        .card-heading {
            margin: 0 0 0.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
        }

        .card-desc {
            margin: 0 0 1rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .title {
            margin: 0 0 0.75rem;
            font-size: 1.65rem;
            font-weight: 700;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 1.25rem;
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .meta span {
            white-space: nowrap;
        }

        .badge-confidential {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.25rem 0.6rem;
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-border);
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .whatsapp-section {
            border: 1px solid #c8e6c9;
            background: linear-gradient(180deg, #f1f8f4 0%, #fff 100%);
            border-radius: 12px;
        }

        .whatsapp-section .card-heading {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .whatsapp-section .card-heading::before {
            content: "";
            width: 28px;
            height: 28px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2325d366'%3E%3Cpath d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z'/%3E%3C/svg%3E") center/contain no-repeat;
        }

        .variant-form {
            margin-bottom: 1rem;
        }

        .variant-form label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: var(--text);
        }

        .variant-form select {
            width: 100%;
            max-width: 20rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            background: var(--card);
            cursor: pointer;
        }

        .share-text-wrap {
            margin-top: 0.75rem;
        }

        .share-text-wrap textarea {
            width: 100%;
            min-height: 200px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.95rem;
            resize: vertical;
            line-height: 1.55;
        }

        .share-text-wrap textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--text);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-family: inherit;
            transition: background .15s, border-color .15s;
        }

        .btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: var(--whatsapp);
            border-color: var(--whatsapp);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--whatsapp-hover);
            border-color: var(--whatsapp-hover);
            color: #fff;
        }

        .btn-pdf {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .btn-pdf:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #fff;
        }

        .copy-feedback {
            font-size: 0.85rem;
            color: var(--success);
            margin-right: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid var(--border);
            padding: 0.5rem 0.65rem;
            vertical-align: top;
        }

        th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .alert-error {
            border-color: var(--error-border);
            background: var(--error-bg);
        }

        .alert-error strong {
            color: var(--error-border);
        }

        .pdf-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
<div class="container">
    @if(session('error'))
        <div class="card alert-error">
            <strong>{{ session('error') }}</strong>
        </div>
    @endif

    <div class="card">
        <h1 class="title">د مجلس شریکول</h1>
        <div class="meta">
            <span><strong>شمېره:</strong> {{ $meeting->meeting_no }}</span>
            <span><strong>عنوان:</strong> {{ $meeting->title }}</span>
            <span><strong>نېټه:</strong> {{ shamsi_date($meeting->date) }}</span>
            <span><strong>ځای:</strong> {{ $meeting->location ?? '-' }}</span>
        </div>
        @if($meeting->is_confidential)
            <span class="badge-confidential">دا مجلس محرم دی</span>
        @endif
    </div>

    <div class="card whatsapp-section">
        <h2 class="card-heading">په WhatsApp کې شریکول</h2>
        <p class="card-desc">لاندې پیغام د مجلس لنډیز لري. د پیغام ډول غوره کړئ، بیا «کاپي» یا «په WhatsApp کې شریکول» وټاکئ ترڅو په واٹس اپ کې وړاندې کړئ.</p>

        <form method="GET" action="{{ route('meetings.share.show', $shareLink->token) }}" class="variant-form">
            <label for="variant">د پیغام ډول</label>
            <select id="variant" name="variant" onchange="this.form.submit()">
                <option value="short" @selected($variant === 'short')>لنډیز — عنوان، نېټه، ځای</option>
                <option value="detailed" @selected($variant === 'detailed')>تفصیلي — پرېکړې، ګډونوال، کارونه</option>
                <option value="tasks_only" @selected($variant === 'tasks_only')>یوازې کارونه</option>
                <option value="absent_only" @selected($variant === 'absent_only')>یوازې غیر حاضر غړي</option>
                <option value="full" @selected($variant === 'full')>بشپړ مجلس — ټول تفصیل</option>
            </select>
        </form>

        <div class="share-text-wrap">
            <textarea id="share-text" readonly>{{ $message }}</textarea>
        </div>

        <div class="buttons">
            <button id="copy-button" class="btn" type="button">کاپي</button>
            <a class="btn btn-primary" href="{{ $whatsAppUrl }}" target="_blank" rel="noopener">په WhatsApp کې شریکول</a>
            <span id="copy-feedback" class="copy-feedback" style="display:none;">کاپي شو</span>
        </div>

        <p class="card-desc" style="margin-top:1rem; margin-bottom:0;">د PDF فایلونو لپاره:</p>
        <div class="pdf-row">
            <a class="btn btn-pdf" href="{{ route('meetings.share.pdf.complete', $shareLink->token) }}" target="_blank" rel="noopener">بشپړ PDF</a>
            <a class="btn btn-pdf" href="{{ route('meetings.share.pdf.decisions', $shareLink->token) }}" target="_blank" rel="noopener">د پرېکړو PDF</a>
            <a class="btn btn-pdf" href="{{ route('meetings.share.pdf.followup', $shareLink->token) }}" target="_blank" rel="noopener">د تعقيب PDF</a>
        </div>
    </div>

    <div class="card">
        <h2 class="card-heading" style="margin-top:0;">کارونه</h2>
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
                    <td>{{ shamsi_date($task->due_date) }}</td>
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
    const copyFeedback = document.getElementById('copy-feedback');

    copyButton.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(shareText.value);
            copyFeedback.style.display = 'inline';
            copyFeedback.textContent = 'کاپي شو';
            const t = setTimeout(() => { copyFeedback.style.display = 'none'; }, 2000);
        } catch (e) {
            shareText.select();
            document.execCommand('copy');
            copyFeedback.style.display = 'inline';
            copyFeedback.textContent = 'کاپي شو';
            const t = setTimeout(() => { copyFeedback.style.display = 'none'; }, 2000);
        }
    });
</script>
</body>
</html>
