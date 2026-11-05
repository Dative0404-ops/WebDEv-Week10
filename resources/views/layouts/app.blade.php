<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PUP Campus Events')</title>
    <style>
        /* ---------- Base ---------- */
        :root {
            --bg: #f4f6fb;
            --card: #ffffff;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --brand: #4338ca;
            --brand-soft: #eef2ff;
            --green: #067647;
            --green-soft: #ecfdf3;
            --amber: #b54708;
            --amber-soft: #fffaeb;
            --blue-soft: #eff8ff;
            --blue: #175cd3;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.5;
        }
        a { color: var(--brand); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* ---------- Top bar ---------- */
        .topbar {
            background: #111827;
            color: #fff;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            gap: 24px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .brand { font-weight: 700; font-size: 18px; letter-spacing: .2px; }
        .brand span { color: #a5b4fc; }
        .topbar nav { margin-left: auto; display: flex; gap: 6px; }
        .topbar nav a {
            color: #d1d5db;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
        }
        .topbar nav a:hover { background: #1f2937; text-decoration: none; }
        .topbar nav a.active { background: var(--brand); color: #fff; }

        /* ---------- Layout ---------- */
        .container { max-width: 1080px; margin: 0 auto; padding: 28px 20px 60px; }
        .page-title { font-size: 26px; margin: 0 0 4px; }
        .page-sub { color: var(--muted); margin: 0 0 24px; }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
        }
        .card h2 { font-size: 16px; margin: 0 0 2px; }
        .card .hint { color: var(--muted); font-size: 13px; margin: 0 0 14px; }
        .qnum {
            display: inline-block;
            background: var(--brand-soft);
            color: var(--brand);
            font-weight: 700;
            font-size: 12px;
            padding: 2px 9px;
            border-radius: 999px;
            margin-right: 8px;
        }

        /* ---------- Tables ---------- */
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid var(--line); }
        th { background: #f9fafb; color: var(--muted); font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
        tr:last-child td { border-bottom: none; }

        /* ---------- Badges ---------- */
        .badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
        }
        .badge.registered { background: var(--green-soft); color: var(--green); }
        .badge.waitlisted { background: var(--amber-soft); color: var(--amber); }
        .badge.type { background: var(--blue-soft); color: var(--blue); }

        .empty { color: var(--muted); font-style: italic; font-size: 14px; }

        /* ---------- Small stats ---------- */
        .stats { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
        .stat {
            flex: 1 1 160px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px 18px;
        }
        .stat .n { font-size: 26px; font-weight: 700; }
        .stat .l { color: var(--muted); font-size: 13px; }

        .callout {
            background: var(--brand-soft);
            border: 1px solid #c7d2fe;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            margin: 0 0 14px;
        }
        footer { color: var(--muted); font-size: 12px; text-align: center; padding: 24px; }
        code { background: #f3f4f6; padding: 1px 6px; border-radius: 6px; font-size: 13px; }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">PUP <span>Campus Events</span></div>
        <nav>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('report') }}" class="{{ request()->routeIs('report') ? 'active' : '' }}">Report</a>
        </nav>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer>
        WebDev Week 10 &middot; Laravel + PostgreSQL relational data model
    </footer>
</body>
</html>
