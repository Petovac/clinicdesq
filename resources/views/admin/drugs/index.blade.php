@extends('admin.layout')

<style>
.kb-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.kb-header h2 { font-size:26px; color:#111827; font-weight:700; margin:0; }
.kb-header .count { font-size:14px; color:#6b7280; margin-left:12px; }
.btn-add { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; background:#2563eb; color:#fff !important; text-decoration:none; border-radius:8px; font-size:14px; font-weight:600; transition:all .15s; }
.btn-add:hover { background:#1d4ed8; transform:translateY(-1px); }

.class-group { background:#fff; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:12px; overflow:hidden; }
.class-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 18px; cursor:pointer; user-select:none;
    background:#f9fafb; border-bottom:1px solid #e5e7eb;
    transition: background 0.15s;
}
.class-header:hover { background:#f1f5f9; }
.class-header h3 { font-size:15px; font-weight:700; color:#1e293b; margin:0; display:flex; align-items:center; gap:10px; }
.class-header .drug-count { background:#e0e7ff; color:#3730a3; font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px; }
.class-header .chevron { font-size:18px; color:#9ca3af; transition:transform 0.2s; }
.class-header.open .chevron { transform:rotate(180deg); }
.class-body { display:none; }
.class-body.open { display:block; }

.drug-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 18px 10px 32px; border-bottom:1px solid #f1f5f9;
    font-size:14px; transition:background 0.1s;
}
.drug-row:last-child { border-bottom:none; }
.drug-row:hover { background:#f8fafc; }
.drug-row a { color:#2563eb; text-decoration:none; font-weight:500; }
.drug-row a:hover { text-decoration:underline; }
.drug-row .meta { font-size:12px; color:#9ca3af; }

.search-box { position:relative; margin-bottom:20px; }
.search-box input {
    width:100%; max-width:400px; padding:10px 14px 10px 38px;
    border:1px solid #d1d5db; border-radius:8px; font-size:14px;
    background:#fff; transition:all 0.15s;
}
.search-box input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.search-box svg { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9ca3af; }
</style>

@section('content')

<div class="kb-header">
    <div style="display:flex;align-items:baseline;">
        <h2>Drug Knowledge Base</h2>
        <span class="count">{{ $drugs->count() }} drugs in {{ $grouped->count() }} classes</span>
    </div>
    <a href="/admin/drugs/create" class="btn-add">+ Add Drug</a>
</div>

<div class="search-box">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
    <input type="text" id="drugSearch" placeholder="Search drugs..." autocomplete="off">
</div>

@foreach($grouped as $className => $classDrugs)
<div class="class-group" data-class="{{ strtolower($className ?? 'uncategorized') }}">
    <div class="class-header" onclick="toggleClass(this)">
        <h3>
            {{ $className ?: 'Uncategorized' }}
            <span class="drug-count">{{ $classDrugs->count() }}</span>
        </h3>
        <span class="chevron">&#9662;</span>
    </div>
    <div class="class-body">
        @foreach($classDrugs as $drug)
        <div class="drug-row" data-name="{{ strtolower($drug->name) }}">
            <a href="/admin/drugs/{{ $drug->id }}/edit">{{ $drug->name }}</a>
            <span class="meta">ID: {{ $drug->id }}</span>
        </div>
        @endforeach
    </div>
</div>
@endforeach

<script>
function toggleClass(header) {
    header.classList.toggle('open');
    header.nextElementSibling.classList.toggle('open');
}

// Search
document.getElementById('drugSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.class-group').forEach(group => {
        let hasMatch = false;
        group.querySelectorAll('.drug-row').forEach(row => {
            const name = row.dataset.name;
            const match = !q || name.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) hasMatch = true;
        });
        group.style.display = hasMatch ? '' : 'none';
        // Auto-open groups when searching
        if (q && hasMatch) {
            group.querySelector('.class-header').classList.add('open');
            group.querySelector('.class-body').classList.add('open');
        } else if (!q) {
            group.querySelector('.class-header').classList.remove('open');
            group.querySelector('.class-body').classList.remove('open');
        }
    });
});
</script>

@endsection
