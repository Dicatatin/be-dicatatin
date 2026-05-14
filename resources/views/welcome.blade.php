<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DICATAT.IN — API</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0D1117;
            --surface: #161B22;
            --border: #21262D;
            --blue: #1B6FEB;
            --green: #1DB954;
            --text: #E6EDF3;
            --text-secondary: #8B949E;
            --text-tertiary: #484F58;
            --font: 'Instrument Sans', system-ui, sans-serif;
        }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            width: 100%;
            max-width: 480px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
        }

        .logo {
            width: 56px;
            height: 56px;
            background: var(--blue);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 600;
            color: white;
            margin: 0 auto 1.5rem;
        }

        h1 {
            font-size: 22px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: .5rem;
        }

        .tagline {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: 13px;
            color: var(--green);
            background: #1DB95415;
            border: 1px solid #1DB95430;
            padding: 6px 14px;
            border-radius: 99px;
            margin-bottom: 2rem;
        }

        .dot {
            width: 7px;
            height: 7px;
            background: var(--green);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .3; }
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin-bottom: 2rem;
        }

        .base-url-label {
            font-size: 11px;
            font-weight: 500;
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: .5rem;
        }

        .base-url {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: var(--green);
            background: #0D1117;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .75rem 1rem;
            margin-bottom: 1.5rem;
            word-break: break-all;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: opacity .15s;
            cursor: pointer;
            border: none;
            width: 100%;
            justify-content: center;
        }

        .btn:hover { opacity: .85; }

        .btn-primary {
            background: var(--blue);
            color: white;
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .footer {
            margin-top: 2rem;
            font-size: 12px;
            color: var(--text-tertiary);
            text-align: center;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">d</div>

    <h1>DICATAT.IN API</h1>
    <p class="tagline">Transformasi catatan tulis tangan menjadi ekosistem pembelajaran terstruktur berbasis AI.</p>

    <div class="status">
        <div class="dot"></div>
        <span>API Operational</span>
    </div>

    <div class="divider"></div>

    <div class="base-url-label">Base URL</div>
    <div class="base-url">{{ config('app.url') }}/api</div>

    <div class="btn-group">
        <a href="/docs" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            API Documentation
        </a>
        <a href="/api/health" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
            Health Check
        </a>
    </div>
</div>

<div class="footer">
    DICATAT.IN &mdash; Universitas Amikom Yogyakarta &copy; 2026
</div>

</body>
</html>
