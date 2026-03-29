@extends('organisation.layout')

@section('content')

<style>
.mod-wrap { max-width: 800px; }
.mod-header h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.mod-header p { color: #6b7280; font-size: 14px; margin: 0 0 24px; }

.mod-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 16px;
    overflow: hidden;
    transition: all .2s;
}
.mod-card.disabled-card { opacity: .65; }

.mod-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f3f4f6;
}
.mod-card-info { flex: 1; }
.mod-card-info h3 { font-size: 16px; font-weight: 700; margin: 0 0 2px; display: flex; align-items: center; gap: 8px; }
.mod-card-info h3 .mod-icon { font-size: 20px; }
.mod-card-info .mod-subtitle { font-size: 13px; color: #6b7280; margin: 0; }

.mod-card-body { padding: 16px 24px; }
.mod-card-body .mod-detail {
    font-size: 13px;
    color: #374151;
    line-height: 1.7;
    padding: 0;
    margin: 0;
}
.mod-card-body .mod-detail li { margin-bottom: 4px; }
.mod-when-off {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 12px;
    font-size: 12px;
    color: #991b1b;
}
.mod-when-on-partial {
    background: #fefce8;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 12px;
    font-size: 12px;
    color: #92400e;
}

/* Toggle switch */
.toggle-switch { position: relative; width: 48px; height: 26px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #d1d5db; border-radius: 999px; transition: .25s;
}
.toggle-slider:before {
    content: ''; position: absolute;
    height: 20px; width: 20px; left: 3px; bottom: 3px;
    background: #fff; border-radius: 50%; transition: .25s;
    box-shadow: 0 1px 3px rgba(0,0,0,.1);
}
.toggle-switch input:checked + .toggle-slider { background: #10b981; }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }

/* Save */
.mod-save-bar {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}
.btn-save {
    background: #2563eb; color: #fff; border: none;
    padding: 10px 28px; border-radius: 8px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    transition: all .15s;
}
.btn-save:hover { background: #1d4ed8; }
.btn-save:disabled { background: #93c5fd; cursor: not-allowed; }

/* Toast */
.mod-toast {
    position: fixed; bottom: 20px; right: 20px;
    padding: 12px 20px; background: #10b981; color: #fff;
    border-radius: 10px; font-size: 14px; font-weight: 500;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    z-index: 999; opacity: 0; transform: translateY(10px);
    transition: all .3s;
}
.mod-toast.show { opacity: 1; transform: translateY(0); }
</style>

<div class="mod-wrap">
    <div class="mod-header">
        <h2>System Modules</h2>
        <p>Configure which features are enabled for your organisation. Disabled modules hide their menus from the sidebar.</p>
    </div>

    {{-- Inventory --}}
    <div class="mod-card" id="card-inventory">
        <div class="mod-card-header">
            <div class="mod-card-info">
                <h3><span class="mod-icon">📦</span> Inventory Management</h3>
                <p class="mod-subtitle">Track stock levels, batches, expiry dates, and transfers to clinics.</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="mod-inventory" {{ ($modules['inventory'] ?? true) ? 'checked' : '' }} onchange="toggleCard('inventory')">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="mod-card-body">
            <ul class="mod-detail">
                <li>Manage inventory items, add stock with batch numbers & expiry dates</li>
                <li>Transfer stock from central to clinics</li>
                <li>Auto-deduct inventory when billing procedures (if linked)</li>
                <li>Quick stock entry for bulk additions</li>
            </ul>
            <div class="mod-when-off">
                <strong>When OFF:</strong> Inventory menu is hidden. Doctors can still prescribe freely — prescriptions are not restricted by inventory.
            </div>
        </div>
    </div>

    {{-- Billing --}}
    <div class="mod-card" id="card-billing">
        <div class="mod-card-header">
            <div class="mod-card-info">
                <h3><span class="mod-icon">💰</span> Billing & Pricing</h3>
                <p class="mod-subtitle">Create price lists, configure fees, generate bills, and track payments.</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="mod-billing" {{ ($modules['billing'] ?? true) ? 'checked' : '' }} onchange="toggleCard('billing')">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="mod-card-body">
            <ul class="mod-detail">
                <li>Price lists with per-item pricing (drugs, services, products)</li>
                <li>Consultation / visit fee configuration</li>
                <li>Injection route administration fees</li>
                <li>Procedure fees with linked consumables</li>
            </ul>
            <div class="mod-when-off">
                <strong>When OFF:</strong> Pricing & Billing menus are hidden. Use your external billing software — the case sheet and prescription are still viewable for reference.
            </div>
            <div class="mod-when-on-partial" id="billing-no-inventory" style="display:none;">
                <strong>Billing without Inventory:</strong> You can define prices directly (name + price) without linking to inventory items. Billing is manual — clinic staff picks items from the price list to create bills. No auto-deduction from stock.
            </div>
        </div>
    </div>

    {{-- Lab --}}
    <div class="mod-card" id="card-lab">
        <div class="mod-card-header">
            <div class="mod-card-info">
                <h3><span class="mod-icon">🔬</span> Lab Management</h3>
                <p class="mod-subtitle">Manage lab test catalog, external labs, and lab technician access.</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="mod-lab" {{ ($modules['lab'] ?? true) ? 'checked' : '' }} onchange="toggleCard('lab')">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <div class="mod-card-body">
            <ul class="mod-detail">
                <li>Define lab test catalog with reference ranges</li>
                <li>Connect with external labs</li>
                <li>Lab technician login to enter results</li>
                <li>Vets can order and review lab results in appointments</li>
            </ul>
            <div class="mod-when-off">
                <strong>When OFF:</strong> Lab menu is hidden. Lab tests can still be noted in the case sheet as free text.
            </div>
        </div>
    </div>

    <div class="mod-save-bar">
        <button type="button" class="btn-save" id="saveBtn" onclick="saveModules()">Save Changes</button>
    </div>
</div>

<div class="mod-toast" id="modToast"></div>

<script>
function toggleCard(module) {
    const card = document.getElementById('card-' + module);
    const checked = document.getElementById('mod-' + module).checked;
    card.classList.toggle('disabled-card', !checked);

    // Show billing-without-inventory notice
    updateBillingNotice();
}

function updateBillingNotice() {
    const invOn = document.getElementById('mod-inventory').checked;
    const billOn = document.getElementById('mod-billing').checked;
    const notice = document.getElementById('billing-no-inventory');
    notice.style.display = (billOn && !invOn) ? 'block' : 'none';
}

function saveModules() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    fetch('{{ route("organisation.settings.modules.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            inventory: document.getElementById('mod-inventory').checked ? 1 : 0,
            billing: document.getElementById('mod-billing').checked ? 1 : 0,
            lab: document.getElementById('mod-lab').checked ? 1 : 0,
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
        if (data.success) {
            showToast('Modules updated. Sidebar will reflect changes on next page load.');
        } else {
            showToast('Error saving modules', true);
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
        showToast('Error saving modules', true);
    });
}

function showToast(msg, isError) {
    const t = document.getElementById('modToast');
    t.textContent = msg;
    t.style.background = isError ? '#ef4444' : '#10b981';
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// Init state
document.addEventListener('DOMContentLoaded', function() {
    ['inventory', 'billing', 'lab'].forEach(m => toggleCard(m));
});
</script>

@endsection
