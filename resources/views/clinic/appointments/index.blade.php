@extends('clinic.layout')

@section('content')

<style>

.container{
max-width:1200px;
margin:auto;
}

.table{
background:#fff;
border-radius:10px;
overflow:hidden;
box-shadow:0 4px 12px rgba(0,0,0,0.05);
}

.table thead{
background:#f7f9fc;
font-weight:600;
}

.table th{
font-size:14px;
padding:14px;
border-bottom:1px solid #eee;
}

.table td{
padding:14px;
vertical-align:middle;
font-size:14px;
}

.table tbody tr{
transition:all .2s ease;
}

.table tbody tr:hover{
background:#fafcff;
}

.badge{
font-size:12px;
padding:6px 10px;
border-radius:20px;
}

.btn{
border-radius:6px;
font-size:13px;
padding:6px 10px;
}

.btn-success{
background:#22c55e;
border:none;
}

.btn-warning{
background:#f59e0b;
border:none;
color:#fff;
}

.btn-danger{
background:#ef4444;
border:none;
}

.btn-primary{
background:#3b82f6;
border:none;
}

h2{
font-weight:600;
font-size:22px;
}

</style>


<div class="container">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="mb-0">Appointments</h2>

@if(auth()->user()->hasPermission('appointments.create'))
<a href="{{ route('clinic.appointments.create') }}"
class="btn btn-success">
+ Create Appointment
</a>
@endif

</div>

<div class="row mb-4">

<div class="col-md-3">
<div class="card text-center shadow-sm">
<div class="card-body">
<h6>Waiting</h6>
<h2 class="text-warning">{{ $waitingCount }}</h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center shadow-sm">
<div class="card-body">
<h6>In Consultation</h6>
<h2 class="text-info">{{ $consultationCount }}</h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center shadow-sm">
<div class="card-body">
<h6>Completed Today</h6>
<h2 class="text-success">{{ $completedCount }}</h2>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center shadow-sm">
<div class="card-body">
<h6>Ready for Billing</h6>
<h2 class="text-danger">{{ $needsBillingCount }}</h2>
</div>
</div>
</div>

</div>

<h4 class="mb-3">Waiting Queue</h4>

<ul class="list-group mb-4">

@foreach($appointments->where('status','checked_in') as $appointment)

<li class="list-group-item d-flex justify-content-between">

<div>
<strong>#{{ $appointment->appointment_number }} {{ $appointment->pet->name }}</strong>
<br>
<small>{{ $appointment->pet->petParent->name }}</small>
</div>

<div>
{{ $appointment->scheduled_at->format('h:i A') }}
</div>

</li>

@endforeach

</ul>

<h4 class="mb-3">In Consultation</h4>

<ul class="list-group mb-4">

@foreach($appointments->where('status','in_consultation') as $appointment)

<li class="list-group-item d-flex justify-content-between">

<div>
<strong>{{ $appointment->pet->name }}</strong>
<br>
<small>{{ $appointment->vet->name }}</small>
</div>

<div>
Started
{{ \Carbon\Carbon::parse($appointment->consultation_started_at)->diffForHumans() }}
</div>

</li>

@endforeach

</ul>

@if($needsBillingCount > 0)
<h4 class="mb-3" style="color:#dc3545;">Ready for Billing ({{ $needsBillingCount }})</h4>

<table class="table table-bordered mb-4">
<thead>
<tr>
<th>#</th>
<th>Pet</th>
<th>Owner</th>
<th>Vet</th>
<th>Completed</th>
<th>Bill Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>
@foreach($needsBilling as $nb)
<tr>
<td>#{{ $nb->appointment_number }}</td>
<td>{{ $nb->pet->name ?? '—' }}</td>
<td>{{ $nb->pet->petParent->name ?? '—' }}</td>
<td>{{ $nb->vet->name ?? 'Unassigned' }}</td>
<td>{{ $nb->completed_at ? \Carbon\Carbon::parse($nb->completed_at)->format('d M h:i A') : '—' }}</td>
<td>
@if($nb->bill && $nb->bill->status == 'draft')
<span class="badge bg-warning">Draft</span>
@else
<span class="badge bg-secondary">No Bill</span>
@endif
</td>
<td>
@if(auth()->user()->hasPermission('billing.create'))
<a href="{{ route('clinic.billing.create', $nb->id) }}" class="btn btn-primary btn-sm">
{{ $nb->bill ? 'Edit Bill' : 'Create Bill' }}
</a>
@elseif(auth()->user()->hasPermission('billing.view') && $nb->bill)
<a href="{{ route('clinic.billing.create', $nb->id) }}" class="btn btn-outline-primary btn-sm">
View Bill
</a>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
@endif

<h4 class="mb-3">All Appointments</h4>

<table class="table table-bordered">

<thead>
<tr>
<th>#</th>
<th>Date & Time</th>
<th>Pet</th>
<th>Owner</th>
<th>Vet</th>
<th>Status</th>
<th>Waiting</th>
<th>Consultation</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

@forelse($appointments as $appointment)

<tr>

<td>#{{ $appointment->appointment_number }}</td>

<td>
{{ $appointment->scheduled_at->format('d M h:i A') }}
</td>

<td>
{{ $appointment->pet->name ?? '—' }}
</td>

<td>
{{ $appointment->pet->petParent->name ?? '—' }}
</td>

<td>
{{ $appointment->vet->name ?? 'Unassigned' }}
</td>

<td>

@php
$statusColors = [
'scheduled' => 'secondary',
'checked_in' => 'warning',
'in_consultation' => 'info',
'awaiting_lab_results' => 'primary',
'completed' => 'success',
'cancelled' => 'danger'
];
@endphp

<span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
{{ ucfirst(str_replace('_',' ',$appointment->status)) }}
</span>

</td>

<td>

@if($appointment->status == 'checked_in')

<span class="waiting-timer"
data-time="{{ \Carbon\Carbon::parse($appointment->checked_in_at)->toIso8601String() }}">
Waiting...
</span>

@endif

</td>

<td>

@if($appointment->consultation_started_at && $appointment->completed_at)

Consult {{ \Carbon\Carbon::parse($appointment->consultation_started_at)->diffInMinutes($appointment->completed_at) }} min

@endif

</td>

<td>

{{-- Check In --}}
@if($appointment->status == 'scheduled' && auth()->user()->hasPermission('appointments.manage'))

<form method="POST"
action="{{ route('clinic.appointments.updateStatus',$appointment->id) }}"
style="display:inline">

@csrf
<input type="hidden" name="status" value="checked_in">

<button class="btn btn-warning btn-sm">
Check In
</button>

</form>

@endif

{{-- Start Consultation --}}
@if($appointment->status == 'checked_in' && auth()->user()->hasPermission('appointments.manage'))

<form method="POST"
action="{{ route('clinic.appointments.updateStatus',$appointment->id) }}"
style="display:inline">

@csrf
<input type="hidden" name="status" value="in_consultation">

<button class="btn btn-info btn-sm">
Start
</button>

</form>

@endif


{{-- Mark Complete / Awaiting Lab --}}
@if($appointment->status == 'in_consultation' && auth()->user()->hasPermission('appointments.manage'))

<form method="POST"
action="{{ route('clinic.appointments.updateStatus',$appointment->id) }}"
style="display:inline">
@csrf
<input type="hidden" name="status" value="completed">
<button class="btn btn-success btn-sm">Complete</button>
</form>

<form method="POST"
action="{{ route('clinic.appointments.updateStatus',$appointment->id) }}"
style="display:inline">
@csrf
<input type="hidden" name="status" value="awaiting_lab_results">
<button class="btn btn-warning btn-sm" title="Mark as done but waiting for lab reports">🔬 Awaiting Lab</button>
</form>

@endif

{{-- Awaiting lab → Complete --}}
@if($appointment->status == 'awaiting_lab_results' && auth()->user()->hasPermission('appointments.manage'))

<form method="POST"
action="{{ route('clinic.appointments.updateStatus',$appointment->id) }}"
style="display:inline">
@csrf
<input type="hidden" name="status" value="completed">
<button class="btn btn-success btn-sm">Mark Complete</button>
</form>

@endif

{{-- Billing --}}
@if(in_array($appointment->status, ['completed','awaiting_lab_results']) && auth()->user()->hasPermission('billing.create'))

<a href="{{ route('clinic.billing.create',$appointment->id) }}"
class="btn btn-primary btn-sm">
Billing
</a>

@endif

{{-- Reschedule --}}
@if(!in_array($appointment->status, ['completed','cancelled','awaiting_lab_results']) && auth()->user()->hasPermission('appointments.manage'))

<button
class="btn btn-secondary btn-sm reschedule-btn"
data-id="{{ $appointment->id }}"
data-pet="{{ $appointment->pet->name }}"
data-owner="{{ $appointment->pet->petParent->name }}"
data-time="{{ $appointment->scheduled_at->format('Y-m-d\TH:i') }}"
>
Reschedule
</button>

@endif


{{-- Cancel --}}
@if(!in_array($appointment->status, ['completed','cancelled','awaiting_lab_results']) && auth()->user()->hasPermission('appointments.manage'))

<form method="POST"
action="{{ route('clinic.appointments.updateStatus',$appointment->id) }}"
style="display:inline">

@csrf
<input type="hidden" name="status" value="cancelled">

<button class="btn btn-danger btn-sm">
Cancel
</button>

</form>

@endif

</td>

</tr>

@empty
<tr>
<td colspan="8" class="text-center">
No appointments scheduled today
</td>
</tr>
@endforelse

</tbody>

</table>

</div>

<div class="modal fade" id="rescheduleModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Reschedule Appointment</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST" id="rescheduleForm">
@csrf

<div class="modal-body">

<p id="modalPet"></p>

<label>Date & Time</label>

<input
type="datetime-local"
name="scheduled_at"
id="modalTime"
class="form-control"
required
>

</div>

<div class="modal-footer">

<button class="btn btn-primary">
Update Appointment
</button>

</div>

</form>

</div>
</div>
</div>


<script>

function updateWaitingTimers(){

document.querySelectorAll('.waiting-timer').forEach(function(el){

let start = new Date(el.dataset.time);
let now = new Date();

let diff = Math.floor((now - start) / 60000);

if(diff < 0) diff = 0;

el.innerText = diff + " min";

});

}

updateWaitingTimers();
setInterval(updateWaitingTimers,10000);

</script>

<script>

document.querySelectorAll('.reschedule-btn').forEach(function(btn){

btn.addEventListener('click', function(){

let id = this.dataset.id;
let pet = this.dataset.pet;
let owner = this.dataset.owner;
let time = this.dataset.time;

document.getElementById('modalPet').innerText =
pet + " (" + owner + ")";

document.getElementById('modalTime').value = time;

document.getElementById('rescheduleForm').action =
"/clinic/appointments/" + id + "/reschedule";

let modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
modal.show();

});

});

</script>

<script>

setInterval(function(){

location.reload();

},30000);

</script>

@endsection