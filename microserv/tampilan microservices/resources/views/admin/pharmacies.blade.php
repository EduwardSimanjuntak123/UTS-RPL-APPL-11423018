@extends('layouts.app')

@section('page-title', 'Pharmacies Management')

@section('sidebar-menu')
    <li>
        <a href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users') }}">
            <i class="bi bi-people nav-icon"></i>
            Users Management
        </a>
    </li>
    <li>
        <a href="{{ route('admin.analytics') }}">
            <i class="bi bi-graph-up nav-icon"></i>
            Analytics
        </a>
    </li>
    <li>
        <a href="{{ route('admin.pharmacies') }}" class="active">
            <i class="bi bi-building nav-icon"></i>
            Pharmacies
        </a>
    </li>
    <li>
        <a href="{{ route('admin.settings') }}">
            <i class="bi bi-gear nav-icon"></i>
            Settings
        </a>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-building"></i> Pharmacies Management
            </div>
            <a href="{{ route('admin.pharmacies.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Add Pharmacy
            </a>
        </div>
    </div>

    <div class="card-body">
        @if(isset($pharmacies) && $pharmacies->count() > 0)
            <div class="row">
                @foreach($pharmacies as $pharmacy)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                    <div>
                                        <h5>{{ $pharmacy->name }}</h5>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $pharmacy->address }}
                                        </small><br>
                                        <small class="text-muted">
                                            <i class="bi bi-telephone"></i> {{ $pharmacy->phone ?? '-' }}
                                        </small>
                                    </div>
                                    <span class="badge" style="background-color: {{ $pharmacy->status === 'active' ? 'green' : 'gray' }};">
                                        {{ ucfirst($pharmacy->status) }}
                                    </span>
                                </div>

                                <hr>

                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div style="font-size: 24px; font-weight: bold; color: #0d6efd;">
                                            {{ $pharmacy->drugStock ? $pharmacy->drugStock->count() : 0 }}
                                        </div>
                                        <small class="text-muted">Drug Items</small>
                                    </div>
                                    <div class="col-6">
                                        <div style="font-size: 24px; font-weight: bold; color: #198754;">
                                            {{ $pharmacy->prescriptionOrders ? $pharmacy->prescriptionOrders->count() : 0 }}
                                        </div>
                                        <small class="text-muted">Orders</small>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 5px;">
                                    <a href="{{ route('admin.pharmacies.edit', $pharmacy) }}" class="btn btn-sm btn-warning flex-grow-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $pharmacy->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No pharmacies found. <a href="{{ route('admin.pharmacies.create') }}">Add one now</a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Modals -->
@if(isset($pharmacies))
    @foreach($pharmacies as $pharmacy)
        <div class="modal fade" id="deleteModal{{ $pharmacy->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Delete Pharmacy</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this pharmacy?</p>
                        <p><strong>{{ $pharmacy->name }}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="{{ route('admin.pharmacies.destroy', $pharmacy) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
