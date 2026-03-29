@extends('organisation.layout')

@section('content')

<style>
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

.page-title{
    font-size:24px;
    font-weight:700;
    color:#111827;
}

.card{
    background:#ffffff;
    padding:28px;
    border-radius:14px;
    box-shadow:0 12px 30px rgba(0,0,0,0.06);
    border:1px solid #f1f5f9;
    margin-bottom:24px;
}

label{
    font-size:13px;
    font-weight:600;
    color:#374151;
    display:block;
    margin-bottom:6px;
}

input,select{
    padding:8px 10px;
    border-radius:8px;
    border:1px solid #d1d5db;
    width:100%;
    font-size:13px;
    background:#fff;
    box-sizing:border-box;
    transition:all .2s ease;
}

input:focus,select:focus{
    outline:none;
    border-color:#4f46e5;
    box-shadow:0 0 0 2px rgba(79,70,229,0.1);
}

/* Table */
.items-table{
    width:100%;
    border-collapse:collapse;
    margin-top:16px;
}

.items-table thead{
    background:#f9fafb;
}

.items-table th{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.05em;
    font-weight:600;
    color:#6b7280;
    padding:10px 8px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
}

.items-table td{
    padding:8px;
    border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
}

.items-table tr:hover{
    background:#fafafa;
}

.items-table input,
.items-table select{
    font-size:12px;
    padding:6px 8px;
}

/* Buttons */
.btn{
    padding:7px 14px;
    border-radius:8px;
    border:none;
    cursor:pointer;
    font-size:13px;
    font-weight:500;
    transition:all .15s ease;
    display:inline-flex;
    align-items:center;
    gap:4px;
}

.btn-primary{ background:#4f46e5; color:#fff; }
.btn-primary:hover{ background:#4338ca; }

.btn-success{ background:#10b981; color:#fff; }
.btn-success:hover{ background:#059669; }

.btn-danger{ background:#ef4444; color:#fff; font-size:12px; padding:5px 10px; }
.btn-danger:hover{ background:#dc2626; }

.btn-secondary{ background:#e5e7eb; color:#374151; }
.btn-secondary:hover{ background:#d1d5db; }

.btn-sm{ padding:5px 10px; font-size:12px; }

/* Add item section */
.add-section{
    background:#eef2ff;
    padding:20px;
    border-radius:12px;
    border:1px solid #c7d2fe;
}

.add-section .add-title{
    font-weight:600;
    font-size:14px;
    color:#3730a3;
    margin-bottom:14px;
}

.add-grid{
    display:grid;
    grid-template-columns:140px 1fr 130px 110px 110px auto;
    gap:10px;
    align-items:end;
}

/* Autocomplete dropdown */
.ac-dropdown{
    display:none;
    position:absolute;
    top:100%;
    left:0;
    right:0;
    background:#fff;
    border:1px solid #d1d5db;
    border-radius:8px;
    max-height:220px;
    overflow-y:auto;
    z-index:100;
    box-shadow:0 4px 12px rgba(0,0,0,.12);
}

.ac-dropdown .ac-item{
    padding:8px 12px;
    cursor:pointer;
    font-size:13px;
    border-bottom:1px solid #f1f5f9;
}

.ac-dropdown .ac-item:hover{
    background:#eff6ff;
}

.ac-dropdown .ac-item strong{
    color:#111827;
}

.ac-dropdown .ac-item .ac-sub{
    color:#6b7280;
    font-size:12px;
}

/* Status toast */
.toast{
    position:fixed;
    bottom:20px;
    right:20px;
    padding:12px 20px;
    background:#10b981;
    color:#fff;
    border-radius:10px;
    font-size:14px;
    font-weight:500;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
    z-index:999;
    opacity:0;
    transform:translateY(10px);
    transition:all .3s ease;
}

.toast.show{
    opacity:1;
    transform:translateY(0);
}

.toast.error{
    background:#ef4444;
}

/* Editable row highlight */
tr.editing td{
    background:#eff6ff;
}

/* Inline drug/inventory display */
.linked-label{
    font-size:12px;
    color:#6b7280;
    display:flex;
    align-items:center;
    gap:4px;
}

.linked-label .dot{
    width:6px;
    height:6px;
    border-radius:50%;
    background:#10b981;
    display:inline-block;
}

@media(max-width:900px){
    .add-grid{
        grid-template-columns:1fr 1fr;
    }
}
</style>


<div class="page-header">
    <h2 class="page-title">Edit Price List</h2>
    <div style="display:flex;gap:8px;">
        <form method="POST" action="{{ route('organisation.price-lists.import-inventory', $priceList) }}">
            @csrf
            <button type="submit" class="btn btn-primary" onclick="return confirm('Import all inventory items not yet in this price list?')">Import from Inventory</button>
        </form>
        <a href="{{ route('organisation.price-lists.index') }}" class="btn btn-secondary">Back to Lists</a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    {{ session('success') }}
</div>
@endif

{{-- Price List Name --}}
<div class="card">
    <label>Price List Name</label>
    <div style="display:flex;gap:10px;align-items:center;max-width:400px;">
        <input type="text" id="listName" value="{{ $priceList->name }}">
        <button class="btn btn-primary btn-sm" onclick="saveName()">Save</button>
    </div>
</div>

{{-- Add New Item --}}
<div class="card add-section">
    <div class="add-title">Add New Item</div>
    <div class="add-grid">
        <div>
            <label>Type</label>
            <select id="newType" onchange="onNewTypeChange()">
                <option value="service">Service</option>
                <option value="drug">Drug</option>
                <option value="vaccine">Vaccine</option>
                <option value="consumable">Consumable</option>
                <option value="surgical">Surgical</option>
                <option value="product">Product</option>
            </select>
        </div>

        <div style="position:relative;">
            <label>Name</label>
            <input id="newName" placeholder="Search or type name..." autocomplete="off">
            <div id="newNameDropdown" class="ac-dropdown"></div>
            <input type="hidden" id="newDrugBrandId">
            <input type="hidden" id="newInventoryItemId">
            <div id="newLinked" style="margin-top:4px;"></div>
        </div>

        <div>
            <label>Billing</label>
            <select id="newBilling">
                <option value="fixed">Fixed</option>
                <option value="per_ml">Per ML</option>
                <option value="per_vial">Per Vial</option>
                <option value="per_tablet">Per Tablet</option>
                <option value="per_unit">Per Unit</option>
                <option value="per_strip">Per Strip</option>
                <option value="per_piece">Per Piece</option>
                <option value="per_sachet">Per Sachet</option>
                <option value="per_tube">Per Tube</option>
                <option value="per_dose">Per Dose</option>
            </select>
        </div>

        <div id="newProcedureWrap">
            <label>Procedure Fee</label>
            <input id="newProcedure" type="number" step="0.01" placeholder="0.00">
        </div>

        <div>
            <label>Unit Price</label>
            <input id="newPrice" type="number" step="0.01" placeholder="0.00">
        </div>

        <div style="padding-top:20px;">
            <button type="button" class="btn btn-success" onclick="addItem()">Add</button>
        </div>
    </div>
</div>

{{-- Existing Items grouped by type --}}
@php
    $typeGroups = $priceList->items->groupBy('item_type');
    $typeLabels = ['drug' => 'Drugs', 'vaccine' => 'Vaccines', 'service' => 'Services', 'consumable' => 'Consumables', 'surgical' => 'Surgical', 'product' => 'Products'];
    $typeColors = ['drug' => ['#dbeafe','#1d4ed8'], 'vaccine' => ['#fef3c7','#b45309'], 'service' => ['#f3e8ff','#7c3aed'], 'consumable' => ['#fef3c7','#92400e'], 'surgical' => ['#fce7f3','#9d174d'], 'product' => ['#d1fae5','#065f46']];
@endphp

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:10px;">
    <div style="font-weight:600;font-size:15px;color:#374151;">
        Items <span id="itemCount" style="color:#9ca3af;font-weight:400;">({{ $priceList->items->count() }})</span>
    </div>
    <input type="text" id="priceSearch" placeholder="Search items..." oninput="filterPriceItems()" style="padding:8px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;width:280px;">
</div>

@forelse($typeGroups as $type => $groupItems)
<div class="card" style="margin-bottom:12px;">
    <div style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding-bottom:10px;border-bottom:1px solid #e5e7eb;margin-bottom:10px;" onclick="let t=this.nextElementSibling;t.style.display=t.style.display==='none'?'block':'none';this.querySelector('.arrow').textContent=t.style.display==='none'?'▸':'▾';">
        <div>
            <span style="background:{{ $typeColors[$type][0] ?? '#f3f4f6' }};color:{{ $typeColors[$type][1] ?? '#374151' }};padding:3px 12px;border-radius:12px;font-size:12px;font-weight:700;">{{ $typeLabels[$type] ?? ucfirst($type) }}</span>
            <span style="font-size:13px;color:#6b7280;margin-left:8px;">({{ $groupItems->count() }} items)</span>
        </div>
        <span class="arrow" style="font-size:14px;color:#9ca3af;">▾</span>
    </div>
    <div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th style="width:110px;">Billing</th>
                    @if($type === 'drug')<th style="width:90px;">Proc. Fee</th>@endif
                    <th style="width:90px;">Unit Price</th>
                    <th style="width:150px;">Linked</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody class="items-body">
                @foreach($groupItems as $item)
                @php
                    $inv = $item->inventoryItem;
                    $strengthLabel = $inv && $inv->strength_value ? $inv->strength_value . ' ' . ($inv->strength_unit ?? '') : '';
                    $formLabel = $inv && $inv->package_type ? ucfirst(str_replace('_', ' ', $inv->package_type)) : '';
                    $packLabel = $inv && $inv->unit_volume_ml ? $inv->unit_volume_ml . ' ' . ($inv->pack_unit ?? '') : '';
                    $multiUse = $inv && $inv->is_multi_use ? true : false;
                @endphp
                <tr data-id="{{ $item->id }}">
                    <td>
                        <div>
                            <input class="f-name" value="{{ $item->name }}" readonly style="font-weight:600;">
                            <input type="hidden" class="f-type" value="{{ $item->item_type }}">
                        </div>
                        @if($strengthLabel || $formLabel || $packLabel)
                        <div style="display:flex;gap:6px;align-items:center;margin-top:3px;flex-wrap:wrap;">
                            @if($formLabel)
                                <span style="background:#f1f5f9;color:#475569;padding:1px 7px;border-radius:4px;font-size:10px;font-weight:600;">{{ $formLabel }}</span>
                            @endif
                            @if($strengthLabel)
                                <span style="font-size:11px;color:#6b7280;">{{ $strengthLabel }}</span>
                            @endif
                            @if($packLabel)
                                <span style="font-size:11px;color:#9ca3af;">| Pack: {{ $packLabel }}</span>
                            @endif
                            @if($multiUse)
                                <span style="background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:600;">Multi-use</span>
                            @endif
                        </div>
                        @endif
                    </td>
                    <td>
                        <select class="f-billing" disabled>
                            @foreach(['fixed'=>'Fixed','per_ml'=>'Per ML','per_vial'=>'Per Vial','per_tablet'=>'Per Tab','per_unit'=>'Per Unit','per_strip'=>'Per Strip','per_piece'=>'Per Pc','per_sachet'=>'Per Sachet','per_tube'=>'Per Tube'] as $bk => $bl)
                                <option value="{{ $bk }}" {{ $item->billing_type==$bk?'selected':'' }}>{{ $bl }}</option>
                            @endforeach
                        </select>
                    </td>
                    @if($type === 'drug')
                    <td><input class="f-procedure" type="number" step="0.01" value="{{ $item->procedure_price }}" readonly></td>
                    @endif
                    <td><input class="f-price" type="number" step="0.01" value="{{ $item->price }}" readonly></td>
                    <td>
                        <input type="hidden" class="f-drug-id" value="{{ $item->drug_brand_id }}">
                        <input type="hidden" class="f-inv-id" value="{{ $item->inventory_item_id }}">
                        <div class="linked-info">
                            @if($inv)
                                <span class="linked-label"><span class="dot"></span> {{ $inv->name }}</span>
                            @elseif($item->drugBrand)
                                <span class="linked-label"><span class="dot"></span> {{ $item->drugBrand->brand_name ?? '' }}</span>
                            @else
                                <span style="color:#d1d5db;font-size:12px;">--</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <button type="button" class="btn btn-secondary btn-sm edit-btn" onclick="toggleEdit(this)">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteItem(this)">x</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card" id="emptyMsg" style="text-align:center;padding:40px 20px;color:#9ca3af;font-size:14px;">
    No items yet. Use the form above or "Import from Inventory" to add items.
</div>
@endforelse

{{-- Toast --}}
<div class="toast" id="toast"></div>

<script>
const CSRF = '{{ csrf_token() }}';
const LIST_ID = {{ $priceList->id }};
const BASE = '/organisation';
const HEADERS = { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' };

// ── Toast ──
function toast(msg, isError){
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast show' + (isError ? ' error' : '');
    setTimeout(() => { t.className = 'toast'; }, 2500);
}

// ── Save price list name ──
function saveName(){
    fetch(`${BASE}/price-lists/${LIST_ID}`, {
        method: 'PUT',
        headers: HEADERS,
        body: JSON.stringify({ name: document.getElementById('listName').value })
    })
    .then(r => r.json())
    .then(r => {
        if(r.success) toast('Name saved');
        else toast('Error saving name', true);
    })
    .catch(() => toast('Error saving name', true));
}

// ── New item type change ──
function onNewTypeChange(){
    const t = document.getElementById('newType').value;
    document.getElementById('newProcedureWrap').style.display = (t === 'drug') ? 'block' : 'none';

    if(t === 'service'){
        document.getElementById('newBilling').value = 'fixed';
    } else if(t === 'drug'){
        document.getElementById('newBilling').value = 'per_ml';
    } else if(t === 'vaccine'){
        document.getElementById('newBilling').value = 'per_dose';
    } else if(t === 'consumable' || t === 'surgical'){
        document.getElementById('newBilling').value = 'fixed';
    } else {
        document.getElementById('newBilling').value = 'per_unit';
    }

    // Clear linked
    document.getElementById('newDrugBrandId').value = '';
    document.getElementById('newInventoryItemId').value = '';
    document.getElementById('newLinked').innerHTML = '';
    document.getElementById('newName').value = '';
}
onNewTypeChange();

// ── Autocomplete for new item name ──
(function(){
    const input = document.getElementById('newName');
    const dd = document.getElementById('newNameDropdown');
    let timer = null;

    input.addEventListener('input', function(){
        clearTimeout(timer);
        const q = this.value.trim();
        if(q.length < 2){ dd.style.display = 'none'; return; }

        timer = setTimeout(function(){
            const type = document.getElementById('newType').value;
            let url;

            if(type === 'drug'){
                url = `${BASE}/drug-search?q=${encodeURIComponent(q)}`;
            } else if(['consumable','surgical','product'].includes(type)){
                url = `${BASE}/price-lists/search-inventory?q=${encodeURIComponent(q)}`;
            } else {
                dd.style.display = 'none';
                return;
            }

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if(!data.length){ dd.style.display = 'none'; return; }

                if(type === 'drug'){
                    dd.innerHTML = data.map(d =>
                        `<div class="ac-item"
                              data-id="${d.id}"
                              data-name="${d.brand} (${d.generic})"
                              data-type="drug">
                            <strong>${d.brand}</strong>
                            <span class="ac-sub"> -- ${d.generic}</span>
                            ${d.strength_value ? `<span class="ac-sub" style="font-size:11px;"> ${d.strength_value}${d.strength_unit||''} ${d.form||''}</span>` : ''}
                        </div>`
                    ).join('');
                } else {
                    dd.innerHTML = data.map(d =>
                        `<div class="ac-item"
                              data-id="${d.id}"
                              data-name="${d.name}"
                              data-type="inventory">
                            <strong>${d.name}</strong>
                            <span class="ac-sub"> (${d.type})</span>
                        </div>`
                    ).join('');
                }

                dd.style.display = 'block';

                dd.querySelectorAll('.ac-item').forEach(el => {
                    el.addEventListener('click', function(){
                        input.value = this.dataset.name;
                        if(this.dataset.type === 'drug'){
                            document.getElementById('newDrugBrandId').value = this.dataset.id;
                            document.getElementById('newInventoryItemId').value = '';
                            document.getElementById('newLinked').innerHTML =
                                `<span class="linked-label"><span class="dot"></span> Drug: ${this.dataset.name}</span>`;
                        } else {
                            document.getElementById('newInventoryItemId').value = this.dataset.id;
                            document.getElementById('newDrugBrandId').value = '';
                            document.getElementById('newLinked').innerHTML =
                                `<span class="linked-label"><span class="dot"></span> Inventory: ${this.dataset.name}</span>`;
                        }
                        dd.style.display = 'none';
                    });
                });
            });
        }, 250);
    });

    document.addEventListener('click', function(e){
        if(!input.contains(e.target) && !dd.contains(e.target)){
            dd.style.display = 'none';
        }
    });
})();

// ── Add item (AJAX) ──
function addItem(){
    const name = document.getElementById('newName').value.trim();
    if(!name){ toast('Enter an item name', true); return; }

    const itemType = document.getElementById('newType').value;
    const drugId = document.getElementById('newDrugBrandId').value;

    if(itemType === 'drug' && !drugId){
        toast('Drug items must have a drug selected from the dropdown', true);
        return;
    }

    const invId = document.getElementById('newInventoryItemId').value;
    const payload = {
        name: name,
        item_type: itemType,
        billing_type: document.getElementById('newBilling').value,
        price: document.getElementById('newPrice').value || 0,
        procedure_price: document.getElementById('newProcedure').value || 0,
    };
    if (drugId) payload.drug_brand_id = drugId;
    if (invId) payload.inventory_item_id = invId;

    fetch(`${BASE}/price-lists/${LIST_ID}/items`, {
        method: 'POST',
        headers: HEADERS,
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(r => {
        if(r.error){ toast(r.error, true); return; }
        if(!r.success){ toast('Error adding item', true); return; }

        const item = r.item;
        const linkedHtml = item.drug_brand_id
            ? `<span class="linked-label"><span class="dot"></span> ${name}</span>`
            : (item.inventory_item_id
                ? `<span class="linked-label"><span class="dot"></span> ${name}</span>`
                : '<span style="color:#d1d5db;font-size:12px;">--</span>');

        const tr = document.createElement('tr');
        tr.dataset.id = item.id;
        tr.innerHTML = `
            <td>
                <select class="f-type" disabled>
                    <option value="service" ${item.item_type==='service'?'selected':''}>Service</option>
                    <option value="drug" ${item.item_type==='drug'?'selected':''}>Drug</option>
                    <option value="consumable" ${item.item_type==='consumable'?'selected':''}>Consumable</option>
                    <option value="surgical" ${item.item_type==='surgical'?'selected':''}>Surgical</option>
                    <option value="product" ${item.item_type==='product'?'selected':''}>Product</option>
                </select>
            </td>
            <td><input class="f-name" value="${item.name}" readonly></td>
            <td>
                <select class="f-billing" disabled>
                    <option value="fixed" ${item.billing_type==='fixed'?'selected':''}>Fixed</option>
                    <option value="per_ml" ${item.billing_type==='per_ml'?'selected':''}>Per ML</option>
                    <option value="per_vial" ${item.billing_type==='per_vial'?'selected':''}>Per Vial</option>
                    <option value="per_tablet" ${item.billing_type==='per_tablet'?'selected':''}>Per Tablet</option>
                    <option value="per_unit" ${item.billing_type==='per_unit'?'selected':''}>Per Unit</option>
                    <option value="per_strip" ${item.billing_type==='per_strip'?'selected':''}>Per Strip</option>
                    <option value="per_piece" ${item.billing_type==='per_piece'?'selected':''}>Per Piece</option>
                    <option value="per_sachet" ${item.billing_type==='per_sachet'?'selected':''}>Per Sachet</option>
                    <option value="per_tube" ${item.billing_type==='per_tube'?'selected':''}>Per Tube</option>
                </select>
            </td>
            <td><input class="f-procedure" type="number" step="0.01" value="${item.procedure_price}" readonly></td>
            <td><input class="f-price" type="number" step="0.01" value="${item.price}" readonly></td>
            <td>
                <input type="hidden" class="f-drug-id" value="${item.drug_brand_id || ''}">
                <input type="hidden" class="f-inv-id" value="${item.inventory_item_id || ''}">
                <div class="linked-info">${linkedHtml}</div>
            </td>
            <td>
                <div style="display:flex;gap:4px;">
                    <button type="button" class="btn btn-secondary btn-sm edit-btn" onclick="toggleEdit(this)">Edit</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteItem(this)">x</button>
                </div>
            </td>
        `;
        document.getElementById('itemsBody').appendChild(tr);

        // Remove empty message
        const em = document.getElementById('emptyMsg');
        if(em) em.remove();

        // Update count
        updateCount();

        // Clear form
        document.getElementById('newName').value = '';
        document.getElementById('newPrice').value = '';
        document.getElementById('newProcedure').value = '';
        document.getElementById('newDrugBrandId').value = '';
        document.getElementById('newInventoryItemId').value = '';
        document.getElementById('newLinked').innerHTML = '';

        toast('Item added');
    })
    .catch(() => toast('Error adding item', true));
}

// ── Inline edit toggle ──
function toggleEdit(btn){
    const row = btn.closest('tr');
    const id = row.dataset.id;

    if(btn.dataset.mode !== 'editing'){
        // Enter edit mode
        row.classList.add('editing');
        row.querySelectorAll('input:not([type=hidden])').forEach(el => el.removeAttribute('readonly'));
        row.querySelectorAll('select').forEach(el => el.removeAttribute('disabled'));
        btn.textContent = 'Save';
        btn.className = 'btn btn-success btn-sm edit-btn';
        btn.dataset.mode = 'editing';
    } else {
        // Save via AJAX
        const procEl = row.querySelector('.f-procedure');
        const drugEl = row.querySelector('.f-drug-id');
        const invEl = row.querySelector('.f-inv-id');
        const data = {
            name: row.querySelector('.f-name').value,
            item_type: row.querySelector('.f-type').value,
            billing_type: row.querySelector('.f-billing').value,
            price: row.querySelector('.f-price').value || 0,
            procedure_price: procEl ? procEl.value || 0 : 0,
            drug_brand_id: (drugEl && drugEl.value) ? drugEl.value : null,
            inventory_item_id: (invEl && invEl.value) ? invEl.value : null,
        };

        fetch(`${BASE}/price-list-items/${id}`, {
            method: 'PUT',
            headers: HEADERS,
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(r => {
            if(r.error){ toast(r.error, true); return; }

            row.classList.remove('editing');
            row.querySelectorAll('input:not([type=hidden])').forEach(el => el.setAttribute('readonly', true));
            row.querySelectorAll('select').forEach(el => el.setAttribute('disabled', true));
            btn.textContent = 'Edit';
            btn.className = 'btn btn-secondary btn-sm edit-btn';
            btn.dataset.mode = '';
            toast('Saved');
        })
        .catch(() => toast('Error saving', true));
    }
}

// ── Delete item ──
function deleteItem(btn){
    if(!confirm('Remove this item?')) return;

    const row = btn.closest('tr');
    const id = row.dataset.id;

    fetch(`${BASE}/price-list-items/${id}`, {
        method: 'DELETE',
        headers: HEADERS
    })
    .then(r => r.json())
    .then(r => {
        if(r.success){
            row.remove();
            updateCount();
            toast('Item removed');
        } else {
            toast('Error removing item', true);
        }
    })
    .catch(() => toast('Error removing item', true));
}

// ── Update item count ──
function updateCount(){
    const count = document.querySelectorAll('#itemsBody tr').length;
    document.getElementById('itemCount').textContent = `(${count})`;
}

// ── Search/filter items ──
function filterPriceItems(){
    const q = document.getElementById('priceSearch').value.toLowerCase();
    document.querySelectorAll('.items-body tr').forEach(row => {
        const name = (row.querySelector('.f-name')?.value || '').toLowerCase();
        const meta = (row.querySelector('td')?.textContent || '').toLowerCase();
        row.style.display = (name.includes(q) || meta.includes(q)) ? '' : 'none';
    });
}
</script>

@endsection
