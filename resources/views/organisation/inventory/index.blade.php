@extends('organisation.layout')

@section('content')

<style>

.container{
max-width:1200px;
margin:auto;
padding:30px;
font-family:Arial, Helvetica, sans-serif;
}

.page-title{
font-size:26px;
font-weight:700;
margin-bottom:20px;
}

.card{
background:#ffffff;
padding:25px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,0.05);
margin-bottom:30px;
}

.form-group{
margin-bottom:16px;
}

.form-label{
display:block;
font-weight:600;
margin-bottom:6px;
font-size:14px;
}

.form-input,
.form-select{
width:100%;
padding:10px;
border:1px solid #ddd;
border-radius:6px;
font-size:14px;
}

.checkbox-group{
margin-bottom:12px;
}

.btn-primary{
background:#2563eb;
color:white;
border:none;
padding:10px 18px;
border-radius:6px;
font-size:14px;
cursor:pointer;
}

.btn-primary:hover{
background:#1d4ed8;
}

.success-box{
background:#e6ffed;
color:#065f46;
padding:10px;
border-radius:6px;
margin-bottom:15px;
}

.table{
width:100%;
border-collapse:collapse;
margin-top:15px;
}

.table th{
background:#f9fafb;
text-align:left;
padding:10px;
font-size:13px;
border-bottom:1px solid #e5e7eb;
}

.table td{
padding:10px;
font-size:14px;
border-bottom:1px solid #f0f0f0;
}

.section-title{
font-size:20px;
font-weight:600;
margin-bottom:15px;
}

</style>

<div class="container">

<div class="page-title">Inventory Management</div>

@if(session('success'))
<div class="success-box">
{{ session('success') }}
</div>
@endif


<div class="card">

<div class="section-title">Add Inventory Item</div>

<form method="POST" action="{{ route('organisation.inventory.store') }}">
@csrf

<div class="form-group">
<label class="form-label">Name</label>
<input class="form-input" type="text" name="name" required>
</div>

<div class="form-group">
<label class="form-label">Item Type</label>
<select class="form-select" name="item_type" required>
<option value="drug">Drug</option>
<option value="consumable">Consumable</option>
</select>
</div>

<div class="form-group">
<label class="form-label">Unit</label>
<input class="form-input" type="text" name="unit">
</div>

<div class="form-group">
<label class="form-label">Package Type</label>
<input class="form-input" type="text" name="package_type">
</div>

<div class="form-group">
<label class="form-label">Strength Value</label>
<input class="form-input" type="text" name="strength_value">
</div>

<div class="form-group">
<label class="form-label">Strength Unit</label>
<input class="form-input" type="text" name="strength_unit">
</div>

<div class="form-group">
<label class="form-label">Unit Volume (ml)</label>
<input class="form-input" type="text" name="unit_volume_ml">
</div>

<div class="checkbox-group">
<label>
<input type="checkbox" name="track_inventory" value="1">
Track Inventory
</label>
</div>

<div class="checkbox-group">
<label>
<input type="checkbox" name="is_multi_use" value="1">
Multi Use (example: vial used multiple times)
</label>
</div>

<button class="btn-primary" type="submit">Add Item</button>

</form>

</div>



<div class="card">

<div class="section-title">Existing Items</div>

<table class="table">

<tr>
<th>Name</th>
<th>Type</th>
<th>Unit</th>
<th>Package</th>
</tr>

@foreach($items as $item)

<tr>
<td>{{ $item->name }}</td>
<td>{{ $item->item_type }}</td>
<td>{{ $item->unit }}</td>
<td>{{ $item->package_type }}</td>
</tr>

@endforeach

</table>

</div>



<div class="card">

<div class="section-title">Add Stock Batch</div>

<form method="POST" action="{{ route('organisation.inventory.batch.store') }}">
@csrf

<div class="form-group">
<label class="form-label">Inventory Item</label>
<select class="form-select" name="inventory_item_id" required>

<option value="">Select Item</option>

@foreach($items as $item)
<option value="{{ $item->id }}">
{{ $item->name }}
</option>
@endforeach

</select>
</div>

<div class="form-group">
<label class="form-label">Batch Number</label>
<input class="form-input" type="text" name="batch_number">
</div>

<div class="form-group">
<label class="form-label">Expiry Date</label>
<input class="form-input" type="date" name="expiry_date">
</div>

<div class="form-group">
<label class="form-label">Quantity</label>
<input class="form-input" type="number" step="0.001" name="quantity" required>
</div>

<div class="form-group">
<label class="form-label">Purchase Price</label>
<input class="form-input" type="number" step="0.01" name="purchase_price">
</div>

<button class="btn-primary" type="submit">Add Batch</button>

</form>

<hr>

<h2>Central Stock</h2>

<table border="1" cellpadding="6">

<tr>
<th>Item</th>
<th>Batch</th>
<th>Expiry</th>
<th>Quantity</th>
<th>Purchase Price</th>
</tr>

@forelse($batches as $batch)

<tr>
<td>{{ $batch->item->name }}</td>
<td>{{ $batch->batch_number }}</td>
<td>{{ $batch->expiry_date }}</td>
<td>{{ $batch->quantity }}</td>
<td>{{ $batch->purchase_price }}</td>
</tr>

@empty

<tr>
<td colspan="5">No stock added yet</td>
</tr>

@endforelse

</table>

</div>

</div>

@endsection