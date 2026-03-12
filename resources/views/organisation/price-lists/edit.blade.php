        @extends('organisation.layout')

        @section('content')

        <style>

        .page-title{
        font-size:24px;
        font-weight:700;
        margin-bottom:24px;
        color:#111827;
        }

        .card{
        background:#ffffff;
        padding:28px;
        border-radius:14px;
        box-shadow:0 12px 30px rgba(0,0,0,0.06);
        border:1px solid #f1f5f9;
        }

        /* form */

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
        transition:all .2s ease;
        }

        input:focus,select:focus{
        outline:none;
        border-color:#4f46e5;
        box-shadow:0 0 0 2px rgba(79,70,229,0.1);
        }

        /* table */

        .items-table{
        width:100%;
        border-collapse:collapse;
        margin-top:20px;
        background:#fff;
        }

        .items-table thead{
        background:#f9fafb;
        }

        .items-table th{
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.04em;
        font-weight:600;
        color:#6b7280;
        padding:10px;
        border-bottom:1px solid #e5e7eb;
        }

        .items-table td{
        padding:10px;
        border-bottom:1px solid #f1f5f9;
        }

        .items-table tr:hover{
        background:#fafafa;
        }

        /* section headers */

        .table-section{
        background:#f3f4f6;
        font-weight:600;
        color:#374151;
        }

        /* buttons */

        .btn{
        padding:8px 14px;
        border-radius:8px;
        border:none;
        cursor:pointer;
        font-size:13px;
        font-weight:500;
        transition:all .15s ease;
        }

        .btn-primary{
        background:#4f46e5;
        color:#fff;
        }

        .btn-primary:hover{
        background:#4338ca;
        }

        .btn-danger{
        background:#ef4444;
        color:#fff;
        }

        .btn-danger:hover{
        background:#dc2626;
        }

        .btn-secondary{
        background:#e5e7eb;
        }

        .btn-secondary:hover{
        background:#d1d5db;
        }

        .actions{
        margin-top:24px;
        display:flex;
        gap:10px;
        }

        </style>


        <h2 class="page-title">Edit Price List</h2>


        <div class="card">

        @if ($errors->any())
            <div style="background:#fee2e2;padding:12px;border-radius:8px;margin-bottom:16px;">
                <ul style="margin:0;padding-left:18px;color:#b91c1c;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('organisation.price-lists.update',$priceList) }}">
        @csrf
        @method('PUT')


        <div style="margin-bottom:20px;">
        <label>Price List Name</label>
        <input name="name" value="{{ $priceList->name }}" required>
        </div>

        <div style="margin-bottom:20px;background:#eef2ff;padding:16px;border-radius:10px;">

                        <div style="font-weight:600;margin-bottom:10px;">
                            ➕ Add New Item
                        </div>

                        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr 1fr auto;gap:10px;align-items:center;">

                            <select id="itemType" name="new_item[item_type]">
                            <option value="service">Service</option>
                            <option value="treatment">Treatment</option>
                            <option value="product">Product</option>
                            </select>

                            <input id="itemName" name="new_item[name]" placeholder="Item name">

                            <select id="billingType" name="new_item[billing_type]">
                            <option value="fixed">Fixed</option>
                            <option value="per_ml">Per ML</option>
                            <option value="per_vial">Per Vial</option>
                            <option value="per_tablet">Per Tablet</option>
                            <option value="per_unit">Per Unit</option>
                            </select>

                            <input id="procedurePrice" name="new_item[procedure_price]" placeholder="Procedure Fee">

                            <input id="unitPrice" name="new_item[price]" placeholder="Unit Price">

                            <select id="drugSelect" name="new_item[drug_brand_id]">
                            <option value="">--</option>
                            @foreach($drugBrands as $drug)
                            <option value="{{ $drug->id }}">{{ $drug->brand_name }}</option>
                            @endforeach
                            </select>

                            <select id="inventorySelect" name="new_item[inventory_item_id]">
                            <option value="">--</option>
                            @foreach($inventoryItems as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                            @endforeach
                            </select>

                            <button class="btn btn-primary">Add</button>

                        </div>

</div>




        <table class="items-table" id="itemsTable">

        <thead>
        <tr>
        <th>Type</th>
        <th>Name</th>
        <th>Billing</th>
        <th>Procedure Fee</th>
        <th>Unit Price</th>
        <th>Drug</th>
        <th>Inventory</th>
        <th width="120">Actions</th>
        </tr>
        </thead>

        <tbody id="itemsBody">


        @if($priceList->items->count())

        <tr style="background:#f3f4f6;">
        <td colspan="8" style="font-weight:600;padding:10px;">
        Existing Items
        </td>
        </tr>

        @endif

        @foreach($priceList->items as $i => $item)

        <tr data-id="{{ $item->id }}">

        <td>
        <select class="field-type" name="items[{{ $i }}][item_type]" disabled>
        <option value="service" {{ $item->item_type=='service'?'selected':'' }}>Service</option>
        <option value="treatment" {{ $item->item_type=='treatment'?'selected':'' }}>Treatment</option>
        <option value="product" {{ $item->item_type=='product'?'selected':'' }}>Product</option>
        </select>
        </td>

        <td>
        <input class="field-name" name="items[{{ $i }}][name]" value="{{ $item->name }}" readonly>
        </td>

        

        <td>
        <select class="field-billing" name="items[{{ $i }}][billing_type]" disabled>
        <option value="fixed" {{ $item->billing_type=='fixed'?'selected':'' }}>Fixed</option>
        <option value="per_ml" {{ $item->billing_type=='per_ml'?'selected':'' }}>Per ML</option>
        <option value="per_vial" {{ $item->billing_type=='per_vial'?'selected':'' }}>Per Vial</option>
        <option value="per_tablet" {{ $item->billing_type=='per_tablet'?'selected':'' }}>Per Tablet</option>
        <option value="per_unit" {{ $item->billing_type=='per_unit'?'selected':'' }}>Per Unit</option>
        </select>
        </td>

        <td>
        <input class="field-procedure" name="items[{{ $i }}][procedure_price]" value="{{ $item->procedure_price }}" readonly>
        </td>

        <td>
        <input class="field-price" name="items[{{ $i }}][price]" value="{{ $item->price }}" readonly>
        </td>

        <td>
        <select class="field-drug" name="items[{{ $i }}][drug_brand_id]" disabled>
        <option value="">--</option>
        @foreach($drugBrands as $drug)
        <option value="{{ $drug->id }}"
        {{ $item->drug_brand_id==$drug->id?'selected':'' }}>
        {{ $drug->brand_name }}
        </option>
        @endforeach
        </select>
        </td>

        <td>
        <select class="field-inventory" name="items[{{ $i }}][inventory_item_id]" disabled>
        <option value="">--</option>
        @foreach($inventoryItems as $inv)
        <option value="{{ $inv->id }}"
        {{ $item->inventory_item_id==$inv->id?'selected':'' }}>
        {{ $inv->name }}
        </option>
        @endforeach
        </select>
        </td>

        <td style="display:flex;gap:6px;">

        <button 
        type="button"
        class="btn btn-secondary"
        onclick="editRow(this)">
        Edit
        </button>

        <button 
        type="button"
        class="btn btn-danger"
        onclick="removeRow(this)">
        ✕
        </button>

        </td>

        </tr>

        @endforeach

        </tbody>
        </table>

        </form>

        </div>

        <script>

                    function editRow(btn){

                    let row = btn.closest('tr');

                    if(btn.dataset.mode !== "editing"){

                        row.querySelectorAll('input').forEach(el=>{
                            el.removeAttribute('readonly');
                        });

                        row.querySelectorAll('select').forEach(el=>{
                            el.removeAttribute('disabled');
                        });

                        btn.innerHTML = "✓";
                        btn.dataset.mode = "editing";
                        btn.style.background = "#10b981";

                    }else{

                        let id = row.dataset.id;

                        let data = {
                            name: row.querySelector('.field-name').value,
                            item_type: row.querySelector('.field-type').value,
                            billing_type: row.querySelector('.field-billing').value,
                            price: row.querySelector('.field-price').value,
                            procedure_price: row.querySelector('.field-procedure').value,
                            drug_brand_id: row.querySelector('.field-drug').value,
                            inventory_item_id: row.querySelector('.field-inventory').value
                        };

                        fetch(`/organisation/price-list-items/${id}`,{
                            method:'PUT',
                            headers:{
                                'Content-Type':'application/json',
                                'X-CSRF-TOKEN':'{{ csrf_token() }}'
                            },
                            body:JSON.stringify(data)
                        })
                        .then(res=>res.json())
                        .then(res=>{

                            row.querySelectorAll('input').forEach(el=>{
                                el.setAttribute('readonly', true);
                            });

                            row.querySelectorAll('select').forEach(el=>{
                                el.setAttribute('disabled', true);
                            });

                            btn.innerHTML = "Edit";
                            btn.dataset.mode = "locked";
                            btn.style.background = "#e5e7eb";

                            btn.innerHTML = "Saved";
                            setTimeout(()=>{ btn.innerHTML="Edit"; },1000);

                        });

                    }
                    }
        </script>

        <script>
                        document.addEventListener("DOMContentLoaded", function(){

                        const type = document.getElementById("itemType");
                        const procedure = document.getElementById("procedurePrice");
                        const drug = document.getElementById("drugSelect");
                        const inventory = document.getElementById("inventorySelect");
                        const billing = document.getElementById("billingType");

                        function updateUI(){

                        let t = type.value;

                        if(t === "service"){

                        procedure.style.display = "none";
                        drug.style.display = "none";
                        inventory.style.display = "none";

                        billing.value = "fixed";

                        }

                        if(t === "treatment"){

                        procedure.style.display = "block";
                        drug.style.display = "block";
                        inventory.style.display = "block";

                        billing.value = "per_ml";

                        }

                        if(t === "product"){

                        procedure.style.display = "none";
                        drug.style.display = "none";
                        inventory.style.display = "block";

                        billing.value = "per_unit";

                        }

                        }

                        type.addEventListener("change", updateUI);

                        updateUI();

                        });

        </script>

        @endsection