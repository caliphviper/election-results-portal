<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') &middot; Election Results Portal</title>
    <style>
        :root {
            --accent: #1d4ed8;
            --accent-soft: #eff6ff;
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --bg: #f9fafb;
            --ok: #047857;
            --ok-soft: #ecfdf5;
            --bad: #b91c1c;
            --bad-soft: #fef2f2;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 15px;
            line-height: 1.55;
        }

        .masthead {
            background: #fff;
            border-bottom: 1px solid var(--line);
        }

        .masthead-inner {
            max-width: 820px;
            margin: 0 auto;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .masthead a.brand {
            font-weight: 700;
            color: var(--ink);
            text-decoration: none;
            letter-spacing: -0.01em;
        }

        .masthead .tag {
            font-size: 12px;
            color: var(--muted);
            border-left: 1px solid var(--line);
            padding-left: 12px;
        }

        main {
            max-width: 820px;
            margin: 0 auto;
            padding: 32px 20px 64px;
        }

        h1 {
            font-size: 26px;
            letter-spacing: -0.02em;
            margin: 0 0 6px;
        }

        .lede {
            color: var(--muted);
            margin: 0 0 28px;
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 22px;
            margin-bottom: 18px;
        }

        .field { margin-bottom: 16px; }
        .field:last-child { margin-bottom: 0; }

        label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .hint {
            font-weight: 400;
            color: var(--muted);
            font-size: 12px;
        }

        select, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: #fff;
            font: inherit;
            color: inherit;
        }

        select:focus, input:focus {
            outline: 2px solid var(--accent);
            outline-offset: -1px;
            border-color: var(--accent);
        }

        select:disabled {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        button {
            padding: 10px 18px;
            background: var(--accent);
            color: #fff;
            border: 0;
            border-radius: 7px;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover:not(:disabled) { background: #1e40af; }
        button:disabled { background: #9ca3af; cursor: not-allowed; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-variant-numeric: tabular-nums;
        }

        th, td {
            padding: 9px 10px;
            text-align: left;
            border-bottom: 1px solid var(--line);
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
        }

        td.num, th.num { text-align: right; }
        tr.leader td { background: var(--accent-soft); font-weight: 600; }
        tfoot td { font-weight: 700; border-bottom: 0; border-top: 2px solid var(--line); }

        .bar {
            height: 6px;
            border-radius: 3px;
            background: var(--accent);
            min-width: 2px;
        }
        .bar-cell { width: 34%; }

        .notice { padding: 12px 14px; border-radius: 8px; margin-bottom: 18px; font-size: 14px; }
        .notice-ok { background: var(--ok-soft); border: 1px solid #a7f3d0; color: var(--ok); }
        .notice-bad { background: var(--bad-soft); border: 1px solid #fecaca; color: var(--bad); }
        .notice ul { margin: 4px 0 0; padding-left: 18px; }

        .muted { color: var(--muted); }
        .empty { color: var(--muted); padding: 18px 0; text-align: center; }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid var(--line);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: -2px;
            margin-right: 8px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .crumb {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 16px;
        }
        .crumb a { color: var(--accent); }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .section-note {
            font-size: 13px;
            color: var(--muted);
            margin: 0 0 14px;
        }

        .party-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
        }

        .party-grid label { font-size: 13px; }

        @media (max-width: 560px) {
            .bar-cell { display: none; }
            main { padding: 22px 16px 48px; }
        }
    </style>
</head>
<body>
    <header class="masthead">
        <div class="masthead-inner">
            <a class="brand" href="{{ url('/') }}">Election Results Portal</a>
            <span class="tag">Delta State &middot; 2011 dataset</span>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <script>
        // Shared helpers for the chained select boxes on all three pages.
        async function getJson(url) {
            const res = await fetch(url);

            if (!res.ok) {
                throw new Error(`server returned ${res.status}`);
            }

            return res.json();
        }

        function resetSelect(select, placeholder) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.disabled = true;
        }

        function fillSelect(select, items, valueOf, labelOf) {
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = valueOf(item);
                opt.textContent = labelOf(item);
                select.appendChild(opt);
            });

            select.disabled = items.length === 0;

            if (items.length === 0) {
                select.innerHTML = '<option value="">-- none available --</option>';
            }
        }

        function showLoading(el, message) {
            el.innerHTML = `<p class="empty"><span class="spinner"></span>${message}</p>`;
        }

        function showError(el, error) {
            el.innerHTML = `<div class="notice notice-bad">Could not load the data (${error.message}). Please try again.</div>`;
        }

        function num(value) {
            return Number(value).toLocaleString();
        }

        // A single-state dataset means the one option may as well be chosen.
        function autoSelectIfOnlyOption(select) {
            const real = [...select.options].filter(o => o.value !== '');

            if (real.length === 1) {
                select.value = real[0].value;
                select.dispatchEvent(new Event('change'));
            }
        }
    </script>

    @yield('scripts')
</body>
</html>
