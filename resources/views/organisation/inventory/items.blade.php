@extends('organisation.layout')

<style>

.container{
max-width:1050px;
margin:auto;
padding:10px 5px;
font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
}

.page-title{
font-size:26px;
font-weight:700;
margin-bottom:25px;
color:#111827;
}

.card{
background:#ffffff;
padding:26px;
border-radius:10px;
border:1px solid #e5e7eb;
margin-bottom:35px;
box-shadow:0 2px 6px rgba(0,0,0,0.04);
}

.card h3{
margin-bottom:18px;
font-size:18px;
font-weight:600;
color:#111827;
}

.form-group{
margin-bottom:18px;
}

.form-group label{
font-weight:600;
display:block;
margin-bottom:6px;
font-size:14px;
color:#374151;
}

.input{
width:100%;
max-width:420px;
padding:9px 10px;
border:1px solid #d1d5db;
border-radius:6px;
font-size:14px;
background:#fff;
transition:all .15s ease;
}

.input:focus{
border-color:#2563eb;
outline:none;
box-shadow:0 0 0 2px rgba(37,99,235,0.15);
}

.select{
padding:9px 10px;
border:1px solid #d1d5db;
border-radius:6px;
width:200px;
font-size:14px;
background:white;
}

.select:focus{
border-color:#2563eb;
outline:none;
box-shadow:0 0 0 2px rgba(37,99,235,0.15);
}

.flex-row{
display:flex;
gap:10px;
align-items:center;
flex-wrap:wrap;
}

.flex-small{
display:flex;
gap:6px;
align-items:center;
}

.input-small{
width:70px;
padding:7px;
border:1px solid #d1d5db;
border-radius:5px;
font-size:13px;
}

.input-medium{
width:100px;
padding:7px;
border:1px solid #d1d5db;
border-radius:5px;
font-size:13px;
}

.btn-primary{
background:#2563eb;
color:#fff;
border:none;
padding:10px 18px;
border-radius:6px;
cursor:pointer;
font-weight:500;
font-size:14px;
transition:all .15s ease;
}

.btn-primary:hover{
background:#1d4ed8;
}

.btn-edit{
background:#2563eb;
color:white;
border:none;
padding:6px 12px;
border-radius:5px;
cursor:pointer;
font-size:13px;
}

.btn-edit:hover{
background:#1d4ed8;
}

.btn-delete{
background:#ef4444;
color:white;
border:none;
padding:6px 12px;
border-radius:5px;
cursor:pointer;
font-size:13px;
margin-left:6px;
}

.btn-delete:hover{
background:#dc2626;
}

.table{
width:100%;
border-collapse:collapse;
background:white;
border:1px solid #e5e7eb;
border-radius:8px;
overflow:hidden;
}

.table th{
background:#f9fafb;
padding:12px;
text-align:left;
font-size:13px;
font-weight:600;
color:#374151;
border-bottom:1px solid #e5e7eb;
}

.table td{
padding:10px;
border-top:1px solid #f1f5f9;
font-size:14px;
color:#111827;
}

.table tr:hover{
background:#f9fafb;
}

.edit-field{
border:none;
background:transparent;
font-size:14px;
}

.edit-field:focus{
outline:none;
}

#drugSuggestions,
#genericSuggestions{
background:white;
border:1px solid #e5e7eb;
border-radius:6px;
max-width:420px;
box-shadow:0 6px 14px rgba(0,0,0,0.08);
}

.drug-option,
.generic-option,
.brand-option{
padding:8px 10px;
font-size:14px;
cursor:pointer;
}

.drug-option:hover,
.generic-option:hover,
.brand-option:hover{
background:#f3f4f6;
}

#brandList{
background:#f9fafb;
padding:15px;
border-radius:8px;
border:1px solid #e5e7eb;
max-width:420px;
}

#brandTitle{
font-size:14px;
font-weight:600;
margin-bottom:8px;
color:#374151;
}

#addNewBrand{
padding:8px;
color:#2563eb;
cursor:pointer;
font-weight:500;
}

#addNewBrand:hover{
text-decoration:underline;
}

</style>


@section('content')

<div class="container">

<h1 class="page-title">Inventory Items</h1>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;padding:12px;border-radius:6px;margin-bottom:15px;color:#166534">

✓ {{ session('success') }}

</div>
@endif

@if ($errors->any())
<div style="background:#fee2e2;border:1px solid #fecaca;padding:12px;border-radius:6px;margin-bottom:15px;color:#991b1b">

<strong>There were some problems:</strong>

<ul style="margin-top:6px;padding-left:18px">

@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach

</ul>

</div>
@endif


<div class="card">

<h3>Add Inventory Item</h3>

<form method="POST" action="{{ route('organisation.inventory.store') }}">
@csrf


<div class="form-group">

<label style="display:block;margin-bottom:6px;">Item Type</label>

<div style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">

<label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
<input type="radio" name="item_type" value="drug" checked>
<span>Drug</span>
</label>

<label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
<input type="radio" name="item_type" value="consumable">
<span>Consumable</span>
</label>

<label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
<input type="radio" name="item_type" value="product">
<span>Product</span>
</label>

<label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
<input type="radio" name="item_type" value="surgical">
<span>Surgical</span>
</label>

</div>

</div>



<!-- DRUG SEARCH -->
<div id="drugFlow">

<div id="drugSearchBox" class="form-group">

<label>Search Drug</label>

<input type="text" id="drugSearch" class="input" placeholder="Search by Brand">

<div id="drugSuggestions"
style="
background:white;
border:1px solid #e5e7eb;
max-width:400px;
position:absolute;
z-index:1000;
">
</div>

</div>


<div class="form-group">

<label>Generic Name</label>

<input type="text"
name="generic_name"
id="genericSearch"
class="input"
placeholder="Search by Generic Name"
autocomplete="off">

<div id="genericSuggestions"
style="
background:white;
border:1px solid #e5e7eb;
max-width:400px;
position:absolute;
z-index:1000;
"></div>

<input type="hidden" name="generic_id" id="generic_id">
<input type="hidden" name="drug_brand_id" id="drug_brand_id">

</div>

<div id="brandList" style="display:none; margin-top:15px;">

<h4 id="brandTitle" style="margin-bottom:8px;"></h4>

<div id="brandOptions"
style="
background:white;
border:1px solid #e5e7eb;
border-radius:6px;
max-width:400px;
"></div>

</div>

</div>





<!-- DRUG FIELDS -->

<div id="drugFields" style="display:none;">

<div class="form-group">

<label>Brand Name</label>

<input type="text" name="name" id="brandSearch" class="input" autocomplete="off">

<div id="brandSuggestions"
style="
background:white;
border:1px solid #e5e7eb;
max-width:400px;
position:absolute;
z-index:1000;
"></div>

</div>


<!-- <div class="form-group">

<label>Unit</label>

<select name="unit" class="select">

<option value="">Select Unit</option>
<option value="ml">ml</option>
<option value="gm">gm</option>
<option value="mg">mg</option>
<option value="tablet">tablet</option>
<option value="capsule">capsule</option>

</select>

</div> -->


<div class="form-group">

<label>Package Type</label>

<select name="package_type" class="select">

<option value="">Select Packaging</option>

<option value="tablet">Tablet</option>
<option value="capsule">Capsule</option>
<option value="injection">Injection</option>
<option value="vial">Vial</option>
<option value="fluid">Fluid</option>
<option value="bottle">Bottle</option>
<option value="strip">Strip</option>

</select>

</div>


<div class="form-group">

<label>Strength</label>

<div class="flex-row">

<input type="text" name="strength_value" class="input">

<select name="strength_unit" class="select">

<option value="">Unit</option>
<option value="mg/ml">mg/ml</option>
<option value="mg">mg</option>
<option value="IU">IU</option>
<option value="%">%</option>

</select>

</div>

</div>


<div class="form-group">

<label>Pack Size</label>

<div class="flex-row">

<input type="text" name="unit_volume_ml" class="input">

<select name="pack_unit" class="select">

<option value="">Unit</option>
<option value="ml">ml</option>
<option value="litre">litre</option>
<option value="mg">mg</option>
<option value="gm">gm</option>
<option value="Kg">Kg</option>

</select>

</div>

</div>

</div>



<!-- CONSUMABLE / PRODUCT / SURGICAL FIELDS -->

<div id="consumableFields">

<div class="form-group">

<label id="consumableNameLabel">Item Name</label>

<input type="text" name="name" class="input">

</div>


<div class="form-group">

<label>Package Type</label>

<select name="package_type" class="select">

<option value="">Select Package</option>

<option value="bottle">Bottle</option>
<option value="packet">Packet</option>
<option value="tube">Tube</option>
<option value="piece">Piece</option>
<option value="sachet">Sachet</option>
<option value="roll">Roll</option>
<option value="box">Box</option>
<option value="canister">Canister</option>
<option value="kit">Kit</option>
<option value="bag">Bag</option>
<option value="pair">Pair</option>

</select>

</div>


<div class="form-group">

<label>Pack Size</label>

<div class="flex-row">

<input type="text" name="unit_volume_ml" class="input">

<select name="pack_unit" class="select">

<option value="">Unit</option>
<option value="ml">ml</option>
<option value="gm">gm</option>
<option value="kg">kg</option>
<option value="piece">piece</option>
<option value="litre">litre</option>
<option value="unit">unit</option>

</select>

</div>

</div>

</div>



<button type="submit" class="btn-primary">
Add Item
</button>

</form>

</div>



<h3>Existing Items</h3>

<table class="table">

<tr>
<th>Name</th>
<th>Type</th>
<th>Package</th>
<th>Strength</th>
<th>Pack Size</th>
<th>Action</th>
</tr>

@foreach($items as $item)

<tr>

<td>

<input class="edit-field input-medium"
name="name"
value="{{ $item->name }}"
data-id="{{ $item->id }}"
readonly>

</td>

<td>{{ $item->item_type }}</td>

<td>

<input class="edit-field input-medium"
name="package_type"
value="{{ $item->package_type }}"
data-id="{{ $item->id }}"
readonly>

</td>

<td>

<div class="flex-small">

<input class="edit-field input-small"
name="strength_value"
value="{{ $item->strength_value }}"
data-id="{{ $item->id }}"
readonly>

<input class="edit-field input-medium"
name="strength_unit"
value="{{ $item->strength_unit }}"
data-id="{{ $item->id }}"
readonly>

</div>

</td>

<td>

<div class="flex-small">

<input class="edit-field input-small"
name="unit_volume_ml"
value="{{ $item->unit_volume_ml }}"
data-id="{{ $item->id }}"
readonly>

<input class="edit-field input-medium"
name="pack_unit"
value="{{ $item->pack_unit }}"
data-id="{{ $item->id }}"
readonly>

</div>

</td>

<td>

<button onclick="editRow(this, {{ $item->id }})" class="btn-edit">
Edit
</button>

<button onclick="deleteItem({{ $item->id }})" class="btn-delete">
Delete
</button>

</td>

</tr>

@endforeach

</table>

</div>

<script>
let existingBrandIds = @json($existingBrandIds);
</script>



<script>

function toggleType(){

let type=document.querySelector('input[name="item_type"]:checked').value

let drugFlow=document.getElementById("drugFlow")
let drugFields=document.getElementById("drugFields")
let consumableFields=document.getElementById("consumableFields")
let nameLabel=document.getElementById("consumableNameLabel")

if(type==="drug"){

drugFlow.style.display="block"
consumableFields.style.display="none"

drugFields.querySelectorAll("input,select").forEach(el=>el.disabled=false)
consumableFields.querySelectorAll("input,select").forEach(el=>el.disabled=true)

}else{

drugFlow.style.display="none"
drugFields.style.display="none"
consumableFields.style.display="block"

drugFields.querySelectorAll("input,select").forEach(el=>el.disabled=true)
consumableFields.querySelectorAll("input,select").forEach(el=>el.disabled=false)

// Update label based on type
let labels = {consumable:"Consumable Name", product:"Product Name", surgical:"Surgical Item Name"}
if(nameLabel) nameLabel.textContent = labels[type] || "Item Name"

}

}

document.addEventListener("DOMContentLoaded", function(){

document.querySelectorAll('input[name="item_type"]').forEach(el=>{
el.addEventListener("change", toggleType)
});

toggleType();

});



function editRow(button,id){

let row=button.closest("tr")
let inputs=row.querySelectorAll(".edit-field")

if(button.innerText==="Edit"){

inputs.forEach(input=>{
input.removeAttribute("readonly")
input.style.border="1px solid #d1d5db"
})

button.innerText="✔"

}else{

let data={}

inputs.forEach(input=>{
data[input.name]=input.value
})

fetch("/organisation/inventory/update/"+id,{
method:"POST",
headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':'{{ csrf_token() }}'
},
body:JSON.stringify(data)
})
.then(res=>res.json())
.then(res=>{

inputs.forEach(input=>{
input.setAttribute("readonly",true)
input.style.border="none"
})

button.innerText="Edit"

})

}

}



function deleteItem(id){

if(!confirm("Delete this item?")) return

fetch("/organisation/inventory/delete/"+id,{
method:"POST",
headers:{
'X-CSRF-TOKEN':'{{ csrf_token() }}'
}
})
.then(()=>location.reload())

}

</script>


<script>

document.getElementById("drugSearch").addEventListener("keyup", function(){

let query = this.value.trim();

if(query.length < 2){
document.getElementById("drugSuggestions").innerHTML="";
return;
}

fetch("/organisation/drug-search?q=" + encodeURIComponent(query))
.then(res => res.json())
.then(data => {

let html="";

data.forEach(drug => {

let label =
drug.brand + " " +
drug.form + " " +
drug.strength_value + drug.strength_unit + " " +
drug.pack_size;

let alreadyExists = existingBrandIds.map(Number).includes(Number(drug.id));

if(alreadyExists){
html += `
<div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-bottom:1px solid #eee;opacity:0.7;">
<span style="font-size:13px;color:#374151;">${label}</span>
<span style="background:#dcfce7;color:#166534;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:600;">Added</span>
</div>
`;
}else{
html += `
<div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-bottom:1px solid #eee;">
<span class="drug-option" style="font-size:13px;color:#374151;cursor:pointer;flex:1;"
data-id="${drug.id}"
data-generic="${drug.generic}"
data-brand="${drug.brand}"
data-strength="${drug.strength_value}"
data-strengthunit="${drug.strength_unit}"
data-pack="${drug.pack_size}"
data-packunit="${drug.pack_unit}"
data-form="${drug.form}">${label}</span>
<button type="button" onclick="quickAddBrand(${drug.id}, '${drug.brand.replace(/'/g,"\\'")}', '${drug.strength_value}', '${drug.strength_unit}', '${drug.form}', '${drug.pack_size}', '${drug.pack_unit}', 0)" style="background:#2563eb;color:#fff;border:none;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;flex-shrink:0;">+ Add</button>
</div>
`;
}

});

document.getElementById("drugSuggestions").innerHTML = html;

});
});

</script>

<script>

document.addEventListener("click", function(e){

if(e.target.classList.contains("drug-option")){

let drug = e.target.dataset;

document.querySelector('[name="generic_name"]').value = drug.generic;
document.querySelector('[name="name"]').value = drug.brand;
document.getElementById("drug_brand_id").value = drug.id;
document.querySelector('[name="strength_value"]').value = drug.strength;
document.querySelector('[name="strength_unit"]').value = drug.strengthunit;
document.querySelector('[name="package_type"]').value = drug.form;
document.querySelector('[name="unit_volume_ml"]').value = drug.pack;
document.querySelector('[name="pack_unit"]').value = drug.packunit;

document.getElementById("drugFields").style.display = "block";

document.getElementById("drugSuggestions").innerHTML = "";

}

});

</script>

<script>

// If user edits drug fields manually, unlink KB drug
document.querySelectorAll(
'[name="generic_name"], [name="name"], [name="strength_value"], [name="strength_unit"], [name="package_type"], [name="unit_volume_ml"], [name="pack_unit"]'
).forEach(el => {

el.addEventListener("input", function(){

let brandIdField = document.getElementById("drug_brand_id")

if(brandIdField){
brandIdField.value = ""
}

})

})
</script>

<script>

document.getElementById("genericSearch").addEventListener("keyup", function(){

let query = this.value.trim();

if(query.length < 2){
document.getElementById("genericSuggestions").innerHTML="";
return;
}

fetch("/organisation/generic-search?q=" + encodeURIComponent(query))
.then(res => res.json())
.then(data => {

    let html="";

if(data.length > 0){

data.forEach(generic => {

html += `
<div class="generic-option"
style="padding:6px;border-bottom:1px solid #eee;cursor:pointer"
data-id="${generic.id}"
data-name="${generic.name}">
${generic.name}
</div>
`;

});

}else{

html += `
<div id="addNewGeneric"
style="padding:8px;color:#2563eb;cursor:pointer;font-weight:500;">
No such generic found. Click to add new drug.
</div>
`;

}

document.getElementById("genericSuggestions").innerHTML = html;

});

});

document.addEventListener("click", function(e){

// EXISTING GENERIC SELECTED
if(e.target.classList.contains("generic-option")){

let generic = e.target.dataset;

document.getElementById("genericSearch").value = generic.name;
document.getElementById("generic_id").value = generic.id;

loadBrands(generic.id, generic.name);

document.getElementById("genericSuggestions").innerHTML = "";

}

// ADD NEW GENERIC
if(e.target.id === "addNewGeneric"){

let genericInput = document.getElementById("genericSearch").value;

// fill generic field
document.querySelector('[name="generic_name"]').value = genericInput;

// clear KB ids
document.getElementById("generic_id").value = "";
document.getElementById("drug_brand_id").value = "";

// show form
document.getElementById("drugFields").style.display = "block";

// hide dropdown
document.getElementById("genericSuggestions").innerHTML = "";

document.querySelector('[name="name"]').focus();

}

});

</script>

<script>

function loadBrands(genericId, genericName){

fetch("/organisation/brands-by-generic?generic_id=" + genericId)
.then(res => res.json())
.then(data => {

let html="";

data.forEach(brand => {

let label =
brand.brand + " " +
brand.form + " " +
brand.strength_value + brand.strength_unit + " " +
brand.pack_size + " " + brand.pack_unit;

let alreadyExists = existingBrandIds.map(Number).includes(Number(brand.id));

if(alreadyExists){
html += `
<div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-bottom:1px solid #eee;opacity:0.7;">
<span style="font-size:13px;color:#374151;">${label}</span>
<span style="background:#dcfce7;color:#166534;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:600;">Added</span>
</div>
`;
}else{
html += `
<div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-bottom:1px solid #eee;">
<span class="brand-option" style="font-size:13px;color:#374151;cursor:pointer;flex:1;"
data-id="${brand.id}"
data-brand="${brand.brand}"
data-strength="${brand.strength_value}"
data-strengthunit="${brand.strength_unit}"
data-form="${brand.form}"
data-pack="${brand.pack_size}"
data-packunit="${brand.pack_unit}">${label}</span>
<button type="button" onclick="quickAddBrand(${brand.id}, '${brand.brand.replace(/'/g,"\\'")}', '${brand.strength_value}', '${brand.strength_unit}', '${brand.form}', '${brand.pack_size}', '${brand.pack_unit}', ${genericId})" style="background:#2563eb;color:#fff;border:none;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;flex-shrink:0;">+ Add</button>
</div>
`;
}

});

html += `
<div id="addNewBrand"
style="padding:8px;color:#2563eb;cursor:pointer;font-weight:500;">
+ Add new brand
</div>
`;

document.getElementById("brandTitle").innerText =
"Available brands for " + genericName;

document.getElementById("brandOptions").innerHTML = html;

document.getElementById("brandList").style.display = "block";

});
}

document.addEventListener("click", function(e){

if(e.target.classList.contains("brand-option")){

let brand = e.target.dataset;

document.querySelector('[name="name"]').value = brand.brand;
document.getElementById("drug_brand_id").value = brand.id;
document.querySelector('[name="strength_value"]').value = brand.strength;
document.querySelector('[name="strength_unit"]').value = brand.strengthunit;
document.querySelector('[name="package_type"]').value = brand.form;
document.querySelector('[name="unit_volume_ml"]').value = brand.pack;
document.querySelector('[name="pack_unit"]').value = brand.packunit;

document.getElementById("drugFields").style.display = "block";

}

// ADD NEW BRAND CLICKED
if(e.target.id==="addNewBrand"){

// clear KB link
document.getElementById("drug_brand_id").value = "";

// clear brand fields
document.querySelector('[name="name"]').value = "";
document.querySelector('[name="strength_value"]').value = "";
document.querySelector('[name="strength_unit"]').value = "";
document.querySelector('[name="package_type"]').value = "";
document.querySelector('[name="unit_volume_ml"]').value = "";
document.querySelector('[name="pack_unit"]').value = "";

// show form
document.getElementById("drugFields").style.display="block";

document.querySelector('[name="name"]').focus();

}

});

function quickAddBrand(brandId, brandName, strengthValue, strengthUnit, form, packSize, packUnit, genericId) {
    let btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Adding...';

    fetch("{{ route('organisation.inventory.store') }}", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            item_type: 'drug',
            name: brandName,
            generic_id: genericId || null,
            drug_brand_id: brandId,
            strength_value: strengthValue,
            strength_unit: strengthUnit,
            package_type: form,
            unit_volume_ml: packSize,
            pack_unit: packUnit,
            quick_add: true,
        })
    })
    .then(res => {
        if (res.ok) {
            btn.textContent = '✓ Added';
            btn.style.background = '#16a34a';
            existingBrandIds.push(Number(brandId));
            // Reload after brief delay
            setTimeout(() => location.reload(), 800);
        } else {
            btn.textContent = 'Error';
            btn.style.background = '#ef4444';
            btn.disabled = false;
            setTimeout(() => { btn.textContent = '+ Add'; btn.style.background = '#2563eb'; }, 2000);
        }
    })
    .catch(() => {
        btn.textContent = '+ Add';
        btn.style.background = '#2563eb';
        btn.disabled = false;
    });
}

</script>

@endsection