<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vet Panel - Clinicdesq</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ================= DESIGN TOKENS ================= */
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-soft: #eff6ff;
            --primary-border: #bfdbfe;

            --success: #16a34a;
            --success-soft: #dcfce7;
            --success-border: #bbf7d0;

            --danger: #dc2626;
            --danger-soft: #fee2e2;
            --danger-border: #fecaca;

            --warning: #f59e0b;
            --warning-soft: #fef3c7;
            --warning-border: #fde68a;

            --bg: #f5f7fa;
            --bg-card: #ffffff;
            --bg-soft: #f9fafb;

            --text: #374151;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --text-light: #9ca3af;

            --border: #e5e7eb;
            --border-light: #f3f4f6;

            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-full: 999px;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 28px rgba(0,0,0,0.1);

            --header-h: 56px;
            --font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* ================= RESET ================= */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--font);
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ================= HEADER ================= */
        .v-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: #1f2937;
            color: #fff;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }

        .v-header .brand {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #fff;
        }

        .v-header nav {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
        }

        .v-header nav a,
        .v-header nav button {
            color: #d1d5db;
            text-decoration: none;
            padding: 6px 12px;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-family: var(--font);
        }

        .v-header nav a:hover,
        .v-header nav button:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        .v-header nav a.active {
            color: #fff;
            background: rgba(255,255,255,0.12);
        }

        .v-header nav a.disabled {
            color: #6b7280;
            pointer-events: none;
        }

        .v-header nav form { margin: 0; }

        /* ================= PAGE ================= */
        .v-page {
            padding: calc(var(--header-h) + 24px) 32px 40px;
            max-width: 1280px;
            margin: 0 auto;
            min-height: 100vh;
        }

        .v-page.v-page--wide {
            max-width: 100%;
            padding-left: 24px;
            padding-right: 24px;
        }

        .v-page.v-page--split {
            max-width: 100%;
            padding-left: 24px;
            padding-right: 24px;
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .v-page--split .v-main { flex: 1; min-width: 0; }
        .v-page--split .v-aside { width: 340px; flex-shrink: 0; position: sticky; top: calc(var(--header-h) + 24px); }

        /* ================= FLASH ================= */
        .v-flash {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 500;
        }

        .v-flash--success {
            background: var(--success-soft);
            color: #065f46;
            border: 1px solid var(--success-border);
        }

        .v-flash--error {
            background: var(--danger-soft);
            color: #7f1d1d;
            border: 1px solid var(--danger-border);
        }

        /* ================= CARD ================= */
        .v-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px 28px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }

        .v-card--compact { padding: 18px 22px; }
        .v-card--flush { padding: 0; overflow: hidden; }

        /* ================= PAGE HEADER ================= */
        .v-page-header {
            margin-bottom: 24px;
        }

        .v-page-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 4px;
        }

        .v-page-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
        }

        .v-page-header--row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ================= BUTTONS ================= */
        .v-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 500;
            font-family: var(--font);
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1.4;
        }

        .v-btn--primary { background: var(--primary); color: #fff; }
        .v-btn--primary:hover { background: var(--primary-hover); }

        .v-btn--success { background: var(--success); color: #fff; }
        .v-btn--success:hover { background: #15803d; }

        .v-btn--danger { background: var(--danger); color: #fff; }
        .v-btn--danger:hover { background: #b91c1c; }

        .v-btn--outline { background: #fff; color: var(--text-dark); border: 1px solid var(--border); }
        .v-btn--outline:hover { background: var(--bg-soft); }

        .v-btn--ghost { background: transparent; color: var(--primary); padding: 6px 10px; }
        .v-btn--ghost:hover { background: var(--primary-soft); }

        .v-btn--sm { padding: 6px 12px; font-size: 13px; }
        .v-btn--block { width: 100%; justify-content: center; }

        /* ================= FORM ================= */
        .v-form-group {
            margin-bottom: 16px;
        }

        .v-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 5px;
        }

        .v-input {
            width: 100%;
            padding: 9px 12px;
            font-size: 14px;
            font-family: var(--font);
            border-radius: var(--radius-sm);
            border: 1px solid #d1d5db;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .v-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37,99,235,0.12);
        }

        textarea.v-input { resize: vertical; min-height: 70px; }
        select.v-input { cursor: pointer; }
        .v-input--readonly { background: var(--bg-soft); cursor: default; }

        .v-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .v-form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .v-form-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ================= TABLE ================= */
        .v-table {
            width: 100%;
            border-collapse: collapse;
        }

        .v-table thead { background: var(--bg-soft); }

        .v-table th {
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
        }

        .v-table td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        .v-table tbody tr:hover { background: var(--bg-soft); }
        .v-table tbody tr:last-child td { border-bottom: none; }

        /* ================= BADGE ================= */
        .v-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
        }

        .v-badge--blue { background: #e0f2fe; color: #0369a1; }
        .v-badge--green { background: var(--success-soft); color: #065f46; }
        .v-badge--red { background: var(--danger-soft); color: #991b1b; }
        .v-badge--yellow { background: var(--warning-soft); color: #92400e; }
        .v-badge--gray { background: #f3f4f6; color: #6b7280; }

        /* ================= LINK ================= */
        .v-link {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            font-size: 14px;
        }

        .v-link:hover { text-decoration: underline; }

        /* ================= EMPTY STATE ================= */
        .v-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .v-empty--bordered {
            background: #fff;
            border: 1px dashed var(--border);
            border-radius: var(--radius-lg);
        }

        /* ================= BANNER ================= */
        .v-banner {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .v-banner--info { background: var(--primary-soft); border: 1px solid var(--primary-border); }

        /* ================= DIVIDER ================= */
        .v-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 20px 0;
        }

        /* ================= GRID ================= */
        .v-grid {
            display: grid;
            gap: 20px;
        }

        .v-grid--2 { grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); }
        .v-grid--3 { grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); }

        /* ================= SECTION TITLE ================= */
        .v-section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin: 0 0 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        /* ================= DETAIL ROW ================= */
        .v-detail-row {
            display: flex;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .v-detail-row dt {
            width: 180px;
            font-weight: 600;
            color: var(--text-dark);
            flex-shrink: 0;
        }

        .v-detail-row dd {
            color: var(--text);
            margin: 0;
        }

        /* ================= BACK LINK ================= */
        .v-back {
            display: inline-block;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 16px;
        }

        .v-back:hover { color: var(--primary); }

        /* ================= CENTERED FORM CARD ================= */
        .v-form-card {
            max-width: 520px;
            margin: 0 auto;
        }

        .v-form-card--narrow { max-width: 420px; }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1024px) {
            .v-page--split { flex-direction: column; }
            .v-page--split .v-aside { width: 100%; position: relative; top: 0; }
        }

        @media (max-width: 768px) {
            .v-header nav { gap: 0; overflow-x: auto; }
            .v-header nav a, .v-header nav button { padding: 6px 8px; font-size: 12px; white-space: nowrap; }
            .v-page { padding-left: 16px; padding-right: 16px; }
            .v-card { padding: 18px; }
            .v-form-row, .v-form-row-3 { grid-template-columns: 1fr; }
            .v-grid--2, .v-grid--3 { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .v-page-header--row { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>

    @yield('head')
</head>
<body>

@php
    use App\Models\Clinic;

    $vet = auth('vet')->user();

    $hasAnyClinic = false;
    if ($vet) {
        $hasAnyClinic = Clinic::whereHas('vets', function ($q) use ($vet) {
            $q->where('clinic_vet.vet_id', $vet->id)
              ->where('clinic_vet.is_active', 1)
              ->whereNull('clinic_vet.offboarded_at');
        })->exists();
    }

    $hasActiveClinic = session()->has('active_clinic_id');

    $aiCreditBalance = 0;
    if ($vet) {
        $aiCreditBalance = \App\Models\VetAiCredit::where('vet_id', $vet->id)->value('balance') ?? 0;
    }
@endphp

{{-- ================= HEADER ================= --}}
<header class="v-header">
    <div class="brand">Clinicdesq</div>

    <nav>
        <a href="{{ route('vet.dashboard') }}"
           class="{{ request()->routeIs('vet.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        @if($hasActiveClinic)
            <a href="{{ route('vet.clinic.dashboard') }}"
               class="{{ request()->routeIs('vet.clinic.*') ? 'active' : '' }}">
                Clinic
            </a>
        @endif

        @if($hasActiveClinic)
            <a href="{{ route('vet.appointments.create') }}"
               class="{{ request()->routeIs('vet.appointments.create') ? 'active' : '' }}">
                New Appointment
            </a>
        @else
            <a class="disabled">New Appointment</a>
        @endif

        <a href="{{ route('vet.appointments.history') }}"
           class="{{ request()->routeIs('vet.appointments.history') ? 'active' : '' }}">
            History
        </a>

        @if($hasActiveClinic)
            <a href="{{ route('vet.lab-orders.index') }}"
               class="{{ request()->routeIs('vet.lab-orders.*') ? 'active' : '' }}">
                Lab Tests
            </a>
        @endif

        @if($hasActiveClinic)
            <a href="{{ route('vet.ipd.index') }}"
               class="{{ request()->routeIs('vet.ipd.*') ? 'active' : '' }}">
                IPD
            </a>
        @endif

        <a href="{{ route('vet.pet.history') }}"
           class="{{ request()->routeIs('vet.pet.*') ? 'active' : '' }}">
            Pet History
        </a>

        @if($hasActiveClinic)
            <a href="{{ url('/vet/schedule') }}"
               class="{{ request()->is('vet/schedule*') ? 'active' : '' }}">
                Schedule
            </a>
        @endif

        <a href="{{ route('vet.jobs.index') }}"
           class="{{ request()->is('vet/jobs*') ? 'active' : '' }}">
            Jobs
        </a>

        <a href="{{ route('vet.profile') }}"
           class="{{ request()->routeIs('vet.profile') ? 'active' : '' }}">
            Profile
        </a>

        <a href="{{ route('vet.credits.index') }}"
           style="display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,0.08);padding:5px 10px;border-radius:var(--radius-sm);text-decoration:none;color:#d1d5db;font-size:12px;">
            <span style="color:#fbbf24;">&#9733;</span>
            <span style="font-weight:700;color:#fff;" class="ai-credit-balance">{{ $aiCreditBalance }}</span>
            <span>credits</span>
        </a>

        @php
            $canSwitchToClinic = false;
            if ($hasActiveClinic && auth('vet')->user()) {
                $canSwitchToClinic = auth('vet')->user()->canManageClinic(session('active_clinic_id'));
            }
        @endphp
        @if($canSwitchToClinic)
            <form method="POST" action="{{ route('vet.switchToClinic') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:var(--primary-soft);color:var(--primary);border:1px solid var(--primary-border);border-radius:var(--radius-sm);padding:8px 14px;font-weight:600;font-size:13px;cursor:pointer;width:100%;text-align:left;">
                    ⇄ Switch to Clinic Panel
                </button>
            </form>
        @endif

        <form method="POST" action="{{ route('vet.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
</header>

{{-- ================= PAGE BODY ================= --}}
<div class="v-page @yield('page-class')">

    @if(session('success'))
        <div class="v-flash v-flash--success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="v-flash v-flash--error">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

@yield('scripts')

{{-- AI Output Markdown Renderer & Styles --}}
<style>
.ai-rendered { font-size: 13px; line-height: 1.7; color: var(--text); }
.ai-rendered h2 { font-size: 15px; font-weight: 700; color: #1e293b; margin: 18px 0 8px; padding-bottom: 6px; border-bottom: 2px solid #e2e8f0; }
.ai-rendered h3 { font-size: 14px; font-weight: 700; color: #334155; margin: 14px 0 6px; }
.ai-rendered h4 { font-size: 13px; font-weight: 600; color: #475569; margin: 10px 0 4px; }
.ai-rendered p { margin: 4px 0 8px; }
.ai-rendered ul, .ai-rendered ol { margin: 4px 0 10px; padding-left: 20px; }
.ai-rendered li { margin-bottom: 3px; }
.ai-rendered li::marker { color: var(--primary); }
.ai-rendered strong { color: #1e293b; font-weight: 600; }
.ai-rendered em { color: #6366f1; font-style: italic; }
.ai-rendered hr { border: none; border-top: 1px dashed #cbd5e1; margin: 14px 0; }
.ai-rendered code { background: #f1f5f9; color: #be185d; padding: 1px 5px; border-radius: 4px; font-size: 12px; }
.ai-rendered blockquote { border-left: 3px solid #6366f1; background: #f8fafc; padding: 8px 12px; margin: 8px 0; border-radius: 0 6px 6px 0; font-size: 12px; color: #475569; }

/* Alert-style blocks: lines starting with specific markers */
.ai-rendered .ai-flag { background: #fef2f2; border: 1px solid #fecaca; border-left: 3px solid #ef4444; padding: 8px 12px; border-radius: 0 6px 6px 0; margin: 8px 0; font-size: 12px; color: #991b1b; }
.ai-rendered .ai-note { background: #eff6ff; border: 1px solid #bfdbfe; border-left: 3px solid #3b82f6; padding: 8px 12px; border-radius: 0 6px 6px 0; margin: 8px 0; font-size: 12px; color: #1e40af; }
.ai-rendered .ai-ok { background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 3px solid #22c55e; padding: 8px 12px; border-radius: 0 6px 6px 0; margin: 8px 0; font-size: 12px; color: #166534; }
.ai-rendered .ai-warn { background: #fffbeb; border: 1px solid #fde68a; border-left: 3px solid #f59e0b; padding: 8px 12px; border-radius: 0 6px 6px 0; margin: 8px 0; font-size: 12px; color: #92400e; }

/* Section cards for numbered headings */
.ai-rendered .ai-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin: 10px 0; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
.ai-rendered .ai-section-title { font-size: 14px; font-weight: 700; color: var(--primary); margin: 0 0 6px; display: flex; align-items: center; gap: 6px; }
.ai-rendered .ai-section-num { background: var(--primary); color: #fff; width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }

.ai-rendered table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 12px; }
.ai-rendered th { background: #f1f5f9; font-weight: 600; text-align: left; padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 11px; text-transform: uppercase; color: #64748b; }
.ai-rendered td { padding: 6px 8px; border: 1px solid #e2e8f0; }
.ai-rendered tr:nth-child(even) td { background: #f9fafb; }

/* Loading animation */
.ai-loading { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 13px; padding: 16px 0; }
.ai-loading-dots span { width: 6px; height: 6px; border-radius: 50%; background: var(--primary); display: inline-block; animation: aiDotPulse 1.4s infinite ease-in-out; }
.ai-loading-dots span:nth-child(2) { animation-delay: 0.2s; }
.ai-loading-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes aiDotPulse { 0%, 80%, 100% { transform: scale(0.4); opacity: 0.4; } 40% { transform: scale(1); opacity: 1; } }
</style>

<script>
/**
 * Lightweight Markdown → HTML renderer for AI output.
 * Handles: headings, bold, italic, lists, tables, blockquotes, hr, code, alerts.
 */
function renderAiMarkdown(text) {
    if (!text) return '<span style="color:var(--text-muted);">No output.</span>';

    // Escape HTML
    let s = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

    // Tables: detect lines with | separators
    s = s.replace(/((?:^|\n)\|.+\|(?:\n\|[-| :]+\|)?\n(?:\|.+\|\n?)+)/g, function(block) {
        const rows = block.trim().split('\n').filter(r => r.trim());
        let html = '<table>';
        rows.forEach((row, i) => {
            if (row.match(/^\|[\s\-:|]+\|$/)) return; // skip separator row
            const cells = row.split('|').filter((c, idx, arr) => idx > 0 && idx < arr.length - 1);
            const tag = i === 0 ? 'th' : 'td';
            html += '<tr>' + cells.map(c => `<${tag}>${c.trim()}</${tag}>`).join('') + '</tr>';
        });
        html += '</table>';
        return '\n' + html + '\n';
    });

    // Split into lines for block-level processing
    const lines = s.split('\n');
    let html = '';
    let inList = false;
    let listType = '';

    for (let i = 0; i < lines.length; i++) {
        let line = lines[i];

        // Skip empty lines
        if (!line.trim()) {
            if (inList) { html += `</${listType}>`; inList = false; }
            continue;
        }

        // Already processed table HTML
        if (line.trim().startsWith('<table') || line.trim().startsWith('</table') || line.trim().startsWith('<tr') || line.trim().startsWith('<th') || line.trim().startsWith('<td')) {
            if (inList) { html += `</${listType}>`; inList = false; }
            html += line;
            continue;
        }

        // Headings
        if (line.match(/^#{1,4}\s/)) {
            if (inList) { html += `</${listType}>`; inList = false; }
            const level = line.match(/^(#+)/)[1].length;
            const text = line.replace(/^#+\s*/, '');
            html += `<h${level + 1}>${applyInline(text)}</h${level + 1}>`;
            continue;
        }

        // Numbered section headings like "**1. Something**" or "1. **Something**"
        const sectionMatch = line.match(/^\*\*(\d+)\.\s*(.+?)\*\*$/) || line.match(/^(\d+)\.\s*\*\*(.+?)\*\*$/);
        if (sectionMatch && !inList) {
            html += `<div class="ai-section"><div class="ai-section-title"><span class="ai-section-num">${sectionMatch[1]}</span>${applyInline(sectionMatch[2])}</div>`;
            // Collect content until next section or end
            let j = i + 1;
            let sectionContent = '';
            while (j < lines.length) {
                const nextSection = lines[j].match(/^\*\*\d+\.\s/) || lines[j].match(/^\d+\.\s*\*\*/);
                const nextHeading = lines[j].match(/^#{1,4}\s/);
                if (nextSection || nextHeading) break;
                sectionContent += lines[j] + '\n';
                j++;
            }
            if (sectionContent.trim()) {
                html += renderAiMarkdown(sectionContent.trim());
            }
            html += '</div>';
            i = j - 1;
            continue;
        }

        // Horizontal rule
        if (line.match(/^---+$/)) {
            if (inList) { html += `</${listType}>`; inList = false; }
            html += '<hr>';
            continue;
        }

        // Blockquote
        if (line.match(/^>\s/)) {
            if (inList) { html += `</${listType}>`; inList = false; }
            html += `<blockquote>${applyInline(line.replace(/^>\s*/, ''))}</blockquote>`;
            continue;
        }

        // Alert blocks: **Flag:** or **Note:** or **Warning:** at start of line
        if (line.match(/^\*\*Flag:?\*\*/i) || line.match(/^-\s*\*\*Flag:?\*\*/i)) {
            if (inList) { html += `</${listType}>`; inList = false; }
            html += `<div class="ai-flag">${applyInline(line.replace(/^-\s*/, ''))}</div>`;
            continue;
        }
        if (line.match(/^\*\*Note:?\*\*/i) || line.match(/^-\s*\*\*Note:?\*\*/i)) {
            if (inList) { html += `</${listType}>`; inList = false; }
            html += `<div class="ai-note">${applyInline(line.replace(/^-\s*/, ''))}</div>`;
            continue;
        }
        if (line.match(/^\*\*Warning:?\*\*/i) || line.match(/^-\s*\*\*Warn/i) || line.match(/^\*\*Caution:?\*\*/i)) {
            if (inList) { html += `</${listType}>`; inList = false; }
            html += `<div class="ai-warn">${applyInline(line.replace(/^-\s*/, ''))}</div>`;
            continue;
        }

        // Unordered list
        if (line.match(/^\s*[-*]\s/)) {
            const indent = line.match(/^(\s*)/)[1].length;
            const content = line.replace(/^\s*[-*]\s+/, '');
            if (!inList || listType !== 'ul') {
                if (inList) html += `</${listType}>`;
                html += '<ul>';
                inList = true;
                listType = 'ul';
            }
            html += `<li>${applyInline(content)}</li>`;
            continue;
        }

        // Ordered list
        if (line.match(/^\s*\d+\.\s/)) {
            const content = line.replace(/^\s*\d+\.\s+/, '');
            if (!inList || listType !== 'ol') {
                if (inList) html += `</${listType}>`;
                html += '<ol>';
                inList = true;
                listType = 'ol';
            }
            html += `<li>${applyInline(content)}</li>`;
            continue;
        }

        // Regular paragraph
        if (inList) { html += `</${listType}>`; inList = false; }
        html += `<p>${applyInline(line)}</p>`;
    }

    if (inList) html += `</${listType}>`;
    return html;
}

/** Inline formatting: bold, italic, code, links */
function applyInline(text) {
    return text
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/__(.+?)__/g, '<strong>$1</strong>')
        .replace(/_(.+?)_/g, '<em>$1</em>');
}

/** Show loading animation in an AI output container */
function showAiLoading(el, message) {
    el.innerHTML = `<div class="ai-loading"><div class="ai-loading-dots"><span></span><span></span><span></span></div>${message || 'Generating...'}</div>`;
    el.classList.add('ai-rendered');
}

/** Render AI response into container */
function setAiOutput(el, text) {
    el.innerHTML = renderAiMarkdown(text);
    el.classList.add('ai-rendered');
}
</script>

</body>
</html>
