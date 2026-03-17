<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('doc-title', 'Document')</title>
    <style>
        @page { size: A4; margin: 15mm 18mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; color: #1a1a1a; line-height: 1.5; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        .print-actions { position: fixed; top: 16px; right: 16px; z-index: 999; display: flex; gap: 8px; }
        .print-actions button { padding: 8px 18px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; cursor: pointer; background: #fff; }
        .print-actions button:hover { background: #f3f4f6; }
        @yield('doc-styles')
    </style>
</head>
<body>
    <div class="print-actions no-print">
        <button onclick="window.print()">Print</button>
    </div>
    @yield('doc-content')
</body>
</html>
