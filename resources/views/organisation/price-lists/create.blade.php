@extends('organisation.layout')

@section('content')

<style>
.page-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
}

.card {
    max-width: 500px;
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 16px;
}

label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 6px;
}

input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #4f46e5;
    color: #fff;
}

.btn-secondary {
    background: #e5e7eb;
}
</style>

<h2 class="page-title">Create Price List</h2>

<div class="card">
    <form method="POST" action="{{ route('organisation.price-lists.store') }}">
        @csrf

        <div class="form-group">
            <label>Price List Name</label>
            <input name="name" required placeholder="e.g. Standard Services">
        </div>

        <div class="actions">
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('organisation.price-lists.index') }}"
               class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection