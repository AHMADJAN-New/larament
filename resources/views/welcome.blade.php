@php
    $locale = app()->getLocale();
    $localeTag = str_replace('_', '-', $locale);
    $isRtl = in_array($locale, ['ps', 'ar', 'fa', 'ur'], true);
@endphp
<!DOCTYPE html>
<html lang="{{ $localeTag }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('welcome.page_title') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --primary: #d97706;
            --primary-dark: #b45309;
            --primary-soft: #fff7ed;
            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.05);
            --shadow-md: 0 10px 24px rgba(16, 24, 40, 0.08);
            --radius-lg: 16px;
            --radius-xl: 24px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Bahij Nassim', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1100px 500px at 8% 0%, rgba(245, 158, 11, 0.14), transparent 45%),
                radial-gradient(900px 420px at 95% 5%, rgba(59, 130, 246, 0.08), transparent 45%),
                var(--bg);
        }

        .container {
            width: min(1120px, 92vw);
            margin-inline: auto;
        }

        .header {
            padding: 22px 0;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--surface);
            border: 1px solid var(--line);
            display: grid;
            place-items: center;
            box-shadow: var(--shadow-sm);
            color: var(--primary);
        }

        .brand-app {
            font-size: 12px;
            color: var(--muted);
        }

        .brand-org {
            font-size: 19px;
            font-weight: 700;
        }

        .btn {
            border: 0;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .btn-outline {
            background: var(--surface);
            color: #374151;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-sm);
        }

        .btn-outline:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .hero {
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid #eef2f7;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            padding: 52px 34px;
            text-align: center;
            backdrop-filter: blur(8px);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            border: 1px solid #fed7aa;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 14px;
            font-weight: 700;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--primary);
        }

        .title {
            margin: 16px 0 10px;
            font-size: clamp(30px, 4vw, 52px);
            line-height: 1.2;
            font-weight: 800;
        }

        .subtitle {
            margin: 0;
            font-size: clamp(18px, 2.2vw, 25px);
            color: #374151;
            font-weight: 700;
        }

        .desc {
            margin: 14px auto 0;
            max-width: 760px;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.9;
        }

        .hero-actions {
            margin-top: 26px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 13px 24px;
            border-radius: 14px;
            box-shadow: 0 10px 20px rgba(217, 119, 6, 0.22);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .section {
            margin-top: 22px;
        }

        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 18px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08);
        }

        .card-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            margin-bottom: 12px;
        }

        .card-icon svg {
            width: 20px;
            height: 20px;
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .card-text {
            margin: 7px 0 0;
            color: var(--muted);
            line-height: 1.7;
            font-size: 15px;
        }

        .stats {
            margin-top: 14px;
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .stat {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .stat-key {
            color: var(--primary);
            font-size: 28px;
            line-height: 1;
            font-weight: 800;
        }

        .stat-value {
            margin-top: 6px;
            font-size: 14px;
            color: var(--muted);
            font-weight: 600;
        }

        .footer {
            padding: 26px 0;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }

        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .header {
                padding: 16px 0;
            }

            .header-inner {
                align-items: flex-start;
            }

            .brand-org {
                font-size: 16px;
            }

            .hero {
                padding: 34px 18px;
                border-radius: 18px;
            }

            .desc {
                font-size: 15px;
            }

            .grid,
            .stats {
                grid-template-columns: 1fr;
            }

            .btn {
                padding: 10px 13px;
                font-size: 14px;
            }
        }

        body[dir="rtl"] { direction: rtl; text-align: right; }
        body[dir="rtl"] .hero { text-align: center; }
    </style>
</head>
<body dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <header class="header">
        <div class="container header-inner">
            <div class="brand">
                <div class="logo" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round">
                        <path d="M12 3l7 6v11H5V9l7-6z" />
                        <path d="M9 20v-7h6v7" />
                    </svg>
                </div>
                <div>
                    <div class="brand-app">{{ __('welcome.app_name') }}</div>
                    <div class="brand-org">{{ __('welcome.organization') }}</div>
                </div>
            </div>

            <a class="btn btn-outline" href="{{ filament()->getPanel('admin')->getLoginUrl() }}">
                <span>{{ __('welcome.login') }}</span>
            </a>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <div class="badge">
                <span class="dot" aria-hidden="true"></span>
                {{ __('welcome.badge') }}
            </div>
            <h1 class="title">{{ __('welcome.title') }}</h1>
            <p class="subtitle">{{ __('welcome.subtitle') }}</p>
            <p class="desc">{{ __('welcome.description') }}</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="{{ filament()->getPanel('admin')->getLoginUrl() }}">
                    {{ __('welcome.cta_panel') }}
                </a>
            </div>
        </section>

        <section class="section">
            <div class="grid">
                <article class="card">
                    <div class="card-icon" style="background:#eff6ff;color:#2563eb" aria-hidden="true">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('welcome.card_meetings_title') }}</h3>
                    <p class="card-text">{{ __('welcome.card_meetings_text') }}</p>
                </article>

                <article class="card">
                    <div class="card-icon" style="background:#ecfdf5;color:#059669" aria-hidden="true">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('welcome.card_decisions_title') }}</h3>
                    <p class="card-text">{{ __('welcome.card_decisions_text') }}</p>
                </article>

                <article class="card">
                    <div class="card-icon" style="background:#fff1f2;color:#e11d48" aria-hidden="true">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('welcome.card_pdf_title') }}</h3>
                    <p class="card-text">{{ __('welcome.card_pdf_text') }}</p>
                </article>

                <article class="card">
                    <div class="card-icon" style="background:#f5f3ff;color:#7c3aed" aria-hidden="true">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <h3 class="card-title">{{ __('welcome.card_share_title') }}</h3>
                    <p class="card-text">{{ __('welcome.card_share_text') }}</p>
                </article>
            </div>
        </section>

        <section class="stats">
            <div class="stat">
                <div class="stat-key">{{ __('welcome.stat_meetings') }}</div>
                <div class="stat-value">{{ __('welcome.stat_meetings_label') }}</div>
            </div>
            <div class="stat">
                <div class="stat-key">{{ __('welcome.stat_decisions') }}</div>
                <div class="stat-value">{{ __('welcome.stat_decisions_label') }}</div>
            </div>
            <div class="stat">
                <div class="stat-key">{{ __('welcome.stat_pdf') }}</div>
                <div class="stat-value">{{ __('welcome.stat_pdf_label') }}</div>
            </div>
        </section>
    </main>

    <footer class="footer">
        {{ __('welcome.footer_copyright', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
    </footer>
</body>
</html>
