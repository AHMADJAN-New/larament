@php
    $formAction = $formAction ?? route('meetings.share.show', $shareLink->token);
@endphp

<style>
    .share-panel {
        --bg: #f3f6fb;
        --card: #ffffff;
        --border: #e2e8f0;
        --text: #1e293b;
        --muted: #64748b;
        --primary: #0ea5e9;
        --primary-hover: #0284c7;
        --success: #25d366;
        --success-hover: #20bd5a;
        --danger-bg: #fef2f2;
        --danger-border: #b91c1c;
        font-family: "Bahij Nassim", "Noto Naskh Arabic", "Segoe UI", sans-serif;
        direction: rtl;
        text-align: right;
        color: var(--text);
        background: var(--bg);
        border-radius: 14px;
        padding: 1rem;
    }

    .share-panel .card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.2rem 1.3rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .share-panel .title {
        margin: 0 0 0.75rem;
        font-size: 1.55rem;
        font-weight: 700;
    }

    .share-panel .meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem 1.2rem;
        font-size: 0.95rem;
        color: var(--muted);
    }

    .share-panel .badge {
        display: inline-block;
        margin-top: 0.6rem;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        border: 1px solid var(--danger-border);
        background: var(--danger-bg);
        color: var(--danger-border);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .share-panel .section-title {
        margin: 0 0 0.4rem;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .share-panel .desc {
        margin: 0 0 0.9rem;
        color: var(--muted);
        font-size: 0.9rem;
    }

    .share-panel label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .share-panel select,
    .share-panel textarea {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: inherit;
        font-size: 0.95rem;
        background: #fff;
    }

    .share-panel select {
        max-width: 21rem;
        padding: 0.5rem 0.7rem;
        margin-bottom: 0.8rem;
    }

    .share-panel textarea {
        min-height: 220px;
        padding: 0.75rem 1rem;
        resize: vertical;
        line-height: 1.55;
    }

    .share-panel select:focus,
    .share-panel textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }

    .share-panel .buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.9rem;
        align-items: center;
    }

    .share-panel .btn {
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text);
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        font-size: 0.92rem;
        padding: 0.45rem 0.95rem;
        transition: 0.15s ease;
    }

    .share-panel .btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .share-panel .btn-success {
        background: var(--success);
        border-color: var(--success);
        color: #fff;
    }

    .share-panel .btn-success:hover {
        background: var(--success-hover);
        border-color: var(--success-hover);
    }

    .share-panel .btn-primary {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .share-panel .btn-primary:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
    }

    .share-panel .copy-feedback {
        color: #059669;
        font-size: 0.85rem;
        margin-inline-start: 0.5rem;
    }

    .share-panel .table-wrap {
        overflow-x: auto;
    }

    .share-panel table {
        width: 100%;
        border-collapse: collapse;
    }

    .share-panel th,
    .share-panel td {
        border: 1px solid var(--border);
        padding: 0.5rem 0.65rem;
        font-size: 0.92rem;
        vertical-align: top;
    }

    .share-panel th {
        background: #f8fafc;
        font-weight: 600;
    }

    .share-panel .alert {
        border-color: var(--danger-border);
        background: var(--danger-bg);
        color: var(--danger-border);
    }
</style>

<div class="share-panel">
    @if(session('error'))
        <div class="card alert">
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
            <span class="badge">دا مجلس محرم دی</span>
        @endif
    </div>

    <div class="card">
        <h2 class="section-title">په WhatsApp کې شریکول</h2>
        <p class="desc">لاندې پیغام د مجلس لنډیز لري. د پیغام ډول غوره کړئ، بیا کاپي یا شریکول وکاروئ.</p>

        <form method="GET" action="{{ $formAction }}">
            <label for="variant">د پیغام ډول</label>
            <select id="variant" name="variant" onchange="this.form.submit()">
                <option value="short" @selected($variant === 'short')>لنډیز — عنوان، نېټه، ځای</option>
                <option value="detailed" @selected($variant === 'detailed')>تفصیلي — پرېکړې، ګډونوال، کارونه</option>
                <option value="tasks_only" @selected($variant === 'tasks_only')>یوازې کارونه</option>
                <option value="absent_only" @selected($variant === 'absent_only')>یوازې غیر حاضر غړي</option>
                <option value="full" @selected($variant === 'full')>بشپړ مجلس — ټول تفصیل</option>
            </select>
        </form>

        <textarea id="share-text" readonly>{{ $message }}</textarea>

        <div class="buttons">
            <button id="copy-button" class="btn" type="button">کاپي</button>
            <a class="btn btn-success" href="{{ $whatsAppUrl }}">په WhatsApp کې شریکول</a>
            <span id="copy-feedback" class="copy-feedback" style="display: none;">کاپي شو</span>
        </div>

        <p class="desc" style="margin-top: 1rem; margin-bottom: 0.4rem;">د PDF فایلونو لپاره:</p>
        <div class="buttons" style="margin-top: 0;">
            <a class="btn btn-primary" href="{{ route('meetings.share.pdf.complete', $shareLink->token) }}">بشپړ PDF</a>
            <a class="btn btn-primary" href="{{ route('meetings.share.pdf.decisions', $shareLink->token) }}">د پرېکړو PDF</a>
            <a class="btn btn-primary" href="{{ route('meetings.share.pdf.followup', $shareLink->token) }}">د تعقيب PDF</a>
        </div>
    </div>

    <div class="card">
        <h2 class="section-title">کارونه</h2>
        <div class="table-wrap">
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButton = document.getElementById('copy-button');
        const shareText = document.getElementById('share-text');
        const copyFeedback = document.getElementById('copy-feedback');

        if (!copyButton || !shareText || !copyFeedback) {
            return;
        }

        copyButton.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(shareText.value);
                copyFeedback.style.display = 'inline';
                setTimeout(function () {
                    copyFeedback.style.display = 'none';
                }, 2000);
            } catch (e) {
                shareText.select();
                document.execCommand('copy');
                copyFeedback.style.display = 'inline';
                setTimeout(function () {
                    copyFeedback.style.display = 'none';
                }, 2000);
            }
        });
    });
</script>
