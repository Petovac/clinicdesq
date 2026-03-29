@extends('organisation.layout')

@section('content')

<style>
.fee-wrap { max-width: 900px; }
.fee-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
.fee-card h3 { margin: 0 0 4px; font-size: 18px; font-weight: 700; }
.fee-card .subtitle { font-size: 13px; color: #6b7280; margin-bottom: 18px; }
.fee-input { padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 7px; font-size: 15px; width: 160px; }
.fee-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.fee-table { width: 100%; border-collapse: collapse; }
.fee-table th { text-align: left; padding: 10px 14px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
.fee-table td { padding: 10px 14px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
.fee-table tr:last-child td { border-bottom: none; }
.btn-save { background: #2563eb; color: #fff; border: none; padding: 9px 22px; border-radius: 7px; font-size: 14px; font-weight: 600; cursor: pointer; transition: opacity .15s; }
.btn-save:hover { opacity: .85; }
.btn-sm-outline { background: #fff; color: #2563eb; border: 1px solid #2563eb; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all .15s; }
.btn-sm-outline:hover { background: #eff6ff; }
.btn-sm-danger { background: #fff; color: #dc2626; border: 1px solid #fca5a5; padding: 4px 10px; border-radius: 5px; font-size: 11px; cursor: pointer; }
.btn-sm-danger:hover { background: #fef2f2; }
.alert-ok { background: #d1fae5; border: 1px solid #6ee7b7; border-radius: 8px; padding: 12px 18px; color: #065f46; font-size: 14px; font-weight: 600; margin-bottom: 18px; }
.alert-err { background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; padding: 12px 18px; color: #991b1b; font-size: 14px; margin-bottom: 18px; }
.route-code { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; background: #eff6ff; color: #1e40af; letter-spacing: .04em; }
.badge-type { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 500; }
.badge-service { background: #fef3c7; color: #92400e; }
.badge-treatment { background: #dbeafe; color: #1e40af; }
.toggle-wrap { display: flex; align-items: center; gap: 8px; }
.toggle-switch { position: relative; width: 40px; height: 22px; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #d1d5db; border-radius: 999px; transition: .2s; }
.toggle-slider:before { content: ''; position: absolute; height: 16px; width: 16px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s; }
.toggle-switch input:checked + .toggle-slider { background: #2563eb; }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(18px); }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:1000; align-items:center; justify-content:center; }
.modal-overlay.active { display:flex; }
.modal-box { background:#fff; border-radius:12px; padding:24px; width:560px; max-width:90vw; max-height:80vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.15); }
.modal-box h4 { margin:0 0 6px; font-size:17px; font-weight:700; }
.modal-box .modal-subtitle { font-size:13px; color:#6b7280; margin-bottom:16px; }
.consumable-search { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; margin-bottom:10px; box-sizing:border-box; }
.consumable-search:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 2px rgba(37,99,235,.1); }
.consumable-checklist { max-height:340px; overflow-y:auto; border:1px solid #e5e7eb; border-radius:8px; }
.cl-row { display:flex; align-items:center; gap:10px; padding:8px 12px; border-bottom:1px solid #f3f4f6; cursor:pointer; transition:background .1s; }
.cl-row:last-child { border-bottom:none; }
.cl-row:hover { background:#f8fafc; }
.cl-row.checked { background:#f0fdf4; }
.cl-row input[type=checkbox] { width:16px; height:16px; cursor:pointer; accent-color:#10b981; flex-shrink:0; }
.cl-name { flex:1; font-size:13px; font-weight:500; color:#1e293b; }
.cl-type { font-size:10px; color:#6b7280; background:#f1f5f9; padding:1px 6px; border-radius:4px; }
.cl-qty { width:65px; padding:5px 8px; border:1px solid #d1d5db; border-radius:5px; font-size:13px; text-align:center; }
.cl-qty:disabled { background:#f9fafb; color:#d1d5db; }
.cl-qty:focus { outline:none; border-color:#10b981; box-shadow:0 0 0 2px rgba(16,163,74,.12); }
.selected-count { font-size:12px; color:#6b7280; padding:6px 0; }
.selected-count strong { color:#10b981; }
.cl-no-results { padding:20px; text-align:center; color:#9ca3af; font-size:13px; }
.modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:18px; padding-top:14px; border-top:1px solid #e5e7eb; }
.btn-cancel { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; padding:8px 18px; border-radius:7px; font-size:13px; cursor:pointer; }
.consumable-count { font-size:11px; color:#6b7280; margin-left:6px; }
</style>

<div class="fee-wrap">

    <h2 style="margin:0 0 6px;font-size:22px;font-weight:700;">Fee Configuration</h2>
    <p style="color:#6b7280;font-size:14px;margin:0 0 24px;">Manage consultation charges, injection route fees, and procedure pricing for your organisation.</p>

    {{-- CONSULTATION FEE --}}
    <div class="fee-card">
        <h3>Consultation / Visit Fee</h3>
        <p class="subtitle">This fee is automatically added to every new bill when a completed appointment is billed.</p>

        @if(session('success'))
            <div class="alert-ok">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-err">{{ session('error') }}</div>
        @endif

        @if($activeList)
        <form method="POST" action="{{ route('organisation.fee-config.visit-fee') }}" style="display:flex;align-items:center;gap:14px;">
            @csrf
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:18px;font-weight:600;color:#6b7280;">&#8377;</span>
                <input type="number" name="visit_fee" class="fee-input"
                       value="{{ $visitFee ? number_format($visitFee->price, 2, '.', '') : '0.00' }}"
                       min="0" step="0.01">
            </div>
            <button type="submit" class="btn-save">Save</button>
        </form>
        @else
        <p style="color:#dc2626;font-size:14px;">No active price list found. Please create a price list first before configuring fees.</p>
        @endif
    </div>

    {{-- INJECTION ROUTE FEES --}}
    <div class="fee-card">
        <h3>Injection Route Administration Fees</h3>
        <p class="subtitle">
            Set the administration fee charged per injection route. The total injection bill is calculated as:<br>
            <strong style="color:#111827;">Total = Route Admin Fee + (Drug Price per ml &times; Volume used)</strong>
        </p>

        @if(session('route_success'))
            <div class="alert-ok">{{ session('route_success') }}</div>
        @endif

        @if($routeFees->count())
        <form method="POST" action="{{ route('organisation.fee-config.routes') }}">
            @csrf
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Full Name</th>
                        <th>Administration Fee</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($routeFees as $i => $route)
                    <input type="hidden" name="fees[{{ $i }}][id]" value="{{ $route->id }}">
                    <tr>
                        <td><span class="route-code">{{ $route->route_code }}</span></td>
                        <td>{{ $route->route_name }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="color:#6b7280;">&#8377;</span>
                                <input type="number" name="fees[{{ $i }}][fee]" class="fee-input" style="width:120px;"
                                       value="{{ number_format($route->administration_fee, 2, '.', '') }}"
                                       min="0" step="0.01">
                            </div>
                        </td>
                        <td>
                            <div class="toggle-wrap">
                                <label class="toggle-switch">
                                    <input type="hidden" name="fees[{{ $i }}][is_active]" value="0">
                                    <input type="checkbox" name="fees[{{ $i }}][is_active]" value="1"
                                           {{ $route->is_active ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div style="margin-top:18px;display:flex;justify-content:flex-end;">
                <button type="submit" class="btn-save">Save Route Fees</button>
            </div>
        </form>
        @else
        <p style="color:#6b7280;font-size:14px;">No injection routes configured. Run the migration to seed default routes.</p>
        @endif
    </div>

    {{-- PROCEDURE & SERVICE FEES --}}
    <div class="fee-card">
        <h3>Procedure &amp; Service Fees</h3>
        <p class="subtitle">Set prices for procedures and services. You can also link consumable inventory items that should be auto-deducted when the procedure is billed.</p>

        @if(session('procedure_success'))
            <div class="alert-ok">{{ session('procedure_success') }}</div>
        @endif

        @if($procedures->count())
        <form method="POST" action="{{ route('organisation.fee-config.procedures') }}">
            @csrf
            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Procedure / Service</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Consumables</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($procedures as $i => $proc)
                    <input type="hidden" name="procedures[{{ $i }}][id]" value="{{ $proc->id }}">
                    <tr>
                        <td style="font-weight:500;">{{ $proc->name }}</td>
                        <td>
                            <span class="badge-type {{ $proc->item_type === 'service' ? 'badge-service' : 'badge-treatment' }}">
                                {{ ucfirst($proc->item_type) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <span style="color:#6b7280;">&#8377;</span>
                                <input type="number" name="procedures[{{ $i }}][price]" class="fee-input" style="width:120px;"
                                       value="{{ number_format($proc->price, 2, '.', '') }}"
                                       min="0" step="0.01">
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn-sm-outline" onclick="openConsumablesModal({{ $proc->id }}, '{{ addslashes($proc->name) }}')">
                                Consumables
                                @if($proc->procedureInventoryItems->count())
                                    <span class="consumable-count">({{ $proc->procedureInventoryItems->count() }})</span>
                                @endif
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div style="margin-top:18px;display:flex;justify-content:flex-end;">
                <button type="submit" class="btn-save">Save Procedure Fees</button>
            </div>
        </form>
        @else
        <p style="color:#6b7280;font-size:14px;">No procedures or services found in your active price list. Add them from the Price List editor first.</p>
        @endif
    </div>

</div>

{{-- CONSUMABLES MODAL --}}
<div class="modal-overlay" id="consumablesModal">
    <div class="modal-box">
        <h4 id="modalTitle">Linked Consumables</h4>
        <p class="modal-subtitle">Check items to link. They will be auto-deducted from inventory when this procedure is billed.</p>

        <input type="text" class="consumable-search" id="consumableSearch" placeholder="Search items..." oninput="filterConsumableList()">
        <div class="selected-count"><strong id="selectedCount">0</strong> items selected</div>

        <div class="consumable-checklist" id="consumableChecklist">
            {{-- Populated by JS --}}
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeConsumablesModal()">Cancel</button>
            <button type="button" class="btn-save" onclick="saveConsumables()">Save Consumables</button>
        </div>
    </div>
</div>

<script>
let currentProcedureId = null;
let currentConsumables = []; // [{inventory_item_id, name, quantity_used}, ...]
const csrfToken = '{{ csrf_token() }}';

// All available consumable/surgical items from backend
const allItems = @json($consumableItems->map(fn($ci) => ['id' => $ci->id, 'name' => $ci->name, 'item_type' => $ci->item_type]));

function openConsumablesModal(procedureId, procedureName) {
    currentProcedureId = procedureId;
    document.getElementById('modalTitle').textContent = 'Consumables for: ' + procedureName;
    document.getElementById('consumableSearch').value = '';

    // Fetch existing consumables
    fetch('/organisation/fee-config/procedures/' + procedureId + '/consumables')
        .then(r => r.json())
        .then(data => {
            currentConsumables = data.map(c => ({
                inventory_item_id: c.inventory_item_id,
                name: c.name,
                quantity_used: c.quantity_used
            }));
            renderChecklist();
            document.getElementById('consumablesModal').classList.add('active');
        });
}

function closeConsumablesModal() {
    document.getElementById('consumablesModal').classList.remove('active');
    currentProcedureId = null;
    currentConsumables = [];
}

function renderChecklist(filter) {
    const container = document.getElementById('consumableChecklist');
    const q = (filter || '').toLowerCase();

    // Filter items
    let items = allItems;
    if (q) {
        items = items.filter(i => i.name.toLowerCase().includes(q));
    }

    if (items.length === 0) {
        container.innerHTML = '<div class="cl-no-results">No items match your search.</div>';
        updateSelectedCount();
        return;
    }

    // Sort: checked items first
    const checkedIds = new Set(currentConsumables.map(c => c.inventory_item_id));
    items = [...items].sort((a, b) => {
        const aChecked = checkedIds.has(a.id) ? 0 : 1;
        const bChecked = checkedIds.has(b.id) ? 0 : 1;
        if (aChecked !== bChecked) return aChecked - bChecked;
        return a.name.localeCompare(b.name);
    });

    let html = '';
    items.forEach(item => {
        const linked = currentConsumables.find(c => c.inventory_item_id === item.id);
        const isChecked = !!linked;
        const qty = linked ? linked.quantity_used : 1;

        html += `
            <div class="cl-row ${isChecked ? 'checked' : ''}" data-id="${item.id}">
                <input type="checkbox" ${isChecked ? 'checked' : ''}
                       onchange="toggleConsumableItem(${item.id}, '${item.name.replace(/'/g, "\\'")}', this.checked)">
                <span class="cl-name">${item.name}</span>
                <span class="cl-type">${item.item_type}</span>
                <input type="number" class="cl-qty" value="${qty}" min="0.001" step="0.001"
                       ${isChecked ? '' : 'disabled'}
                       onchange="updateConsumableQty(${item.id}, this.value)"
                       onclick="event.stopPropagation()">
            </div>
        `;
    });
    container.innerHTML = html;

    // Click row to toggle checkbox
    container.querySelectorAll('.cl-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT') return; // Don't double-toggle
            const cb = this.querySelector('input[type=checkbox]');
            cb.checked = !cb.checked;
            cb.dispatchEvent(new Event('change'));
        });
    });

    updateSelectedCount();
}

function toggleConsumableItem(itemId, itemName, isChecked) {
    if (isChecked) {
        if (!currentConsumables.find(c => c.inventory_item_id === itemId)) {
            currentConsumables.push({
                inventory_item_id: itemId,
                name: itemName,
                quantity_used: 1
            });
        }
    } else {
        currentConsumables = currentConsumables.filter(c => c.inventory_item_id !== itemId);
    }

    // Update row style and qty input
    const row = document.querySelector(`.cl-row[data-id="${itemId}"]`);
    if (row) {
        row.classList.toggle('checked', isChecked);
        const qtyInput = row.querySelector('.cl-qty');
        if (isChecked) {
            qtyInput.disabled = false;
            qtyInput.value = 1;
        } else {
            qtyInput.disabled = true;
        }
    }
    updateSelectedCount();
}

function updateConsumableQty(itemId, value) {
    const linked = currentConsumables.find(c => c.inventory_item_id === itemId);
    if (linked) {
        linked.quantity_used = parseFloat(value) || 1;
    }
}

function filterConsumableList() {
    const q = document.getElementById('consumableSearch').value;
    renderChecklist(q);
}

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = currentConsumables.length;
}

function saveConsumables() {
    let payload = currentConsumables.map(c => ({
        inventory_item_id: c.inventory_item_id,
        quantity_used: c.quantity_used
    }));

    fetch('/organisation/fee-config/procedures/' + currentProcedureId + '/consumables', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ consumables: payload })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeConsumablesModal();
            location.reload();
        } else {
            alert(data.error || 'Failed to save.');
        }
    })
    .catch(() => alert('Network error. Please try again.'));
}

// Close modal on overlay click
document.getElementById('consumablesModal').addEventListener('click', function(e) {
    if (e.target === this) closeConsumablesModal();
});
</script>

@endsection
