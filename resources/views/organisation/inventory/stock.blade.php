@extends('organisation.layout')

@section('content')

<div class="container">

<h1>Stock Management</h1>

@foreach($items as $item)

<div class="card" style="margin-bottom:25px; padding:20px; border:1px solid #ddd;">

<h3>{{ $item->name }}</h3>

<p>
Current Stock:
<strong>{{ $item->batches->sum('quantity') }}</strong>
</p>


@if($item->batches->count())

<table style="margin-bottom:15px; border-collapse:collapse; width:100%;">

<tr style="background:#f3f4f6;">
<th style="padding:6px; border:1px solid #ddd;">Batch</th>
<th style="padding:6px; border:1px solid #ddd;">Qty</th>
<th style="padding:6px; border:1px solid #ddd;">Expiry</th>
</tr>

@foreach($item->batches as $batch)

<tr>
<td style="padding:6px; border:1px solid #ddd;">
{{ $batch->batch_number }}
</td>

<td style="padding:6px; border:1px solid #ddd;">
{{ $batch->quantity }}
</td>

<td style="padding:6px; border:1px solid #ddd;">
{{ $batch->expiry_date }}
</td>
</tr>

@endforeach

</table>

@endif


<form method="POST" action="{{ route('organisation.inventory.batch.store') }}">

@csrf

<input type="hidden" name="inventory_item_id" value="{{ $item->id }}">

<div style="display:flex; gap:10px; flex-wrap:wrap;">

<input
type="text"
name="batch_number"
placeholder="Batch Number"
required
>

<input
type="date"
name="expiry_date"
>

<input
type="number"
step="0.001"
name="quantity"
placeholder="Quantity"
required
>

<input
type="number"
step="0.01"
name="purchase_price"
placeholder="Purchase Price"
>

<button type="submit">
Add
</button>

</div>

</form>

</div>

@endforeach

</div>

@endsection