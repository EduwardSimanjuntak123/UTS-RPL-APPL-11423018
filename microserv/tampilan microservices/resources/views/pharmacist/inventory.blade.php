@extends('layouts.app')

@section('page-title', 'Drug Inventory')

@section('sidebar-menu')
    <li>
        <a href="{{ route('pharmacist.dashboard') }}">
            <i class="bi bi-speedometer2 nav-icon"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('pharmacist.inventory') }}" class="active">
            <i class="bi bi-box-seam nav-icon"></i>
            Inventory
        </a>
    </li>
    <li>
        <a href="{{ route('pharmacist.orders') }}">
            <i class="bi bi-bag nav-icon"></i>
            Prescription Orders
        </a>
    </li>
    <li>
        <a href="{{ route('pharmacist.profile') }}">
            <i class="bi bi-person nav-icon"></i>
            Profile
        </a>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-box-seam"></i> Drug Inventory
            </div>
            <a href="{{ route('pharmacist.inventory.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Add Drug
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <input type="text" class="form-control" id="searchDrugs" placeholder="Search by drug name, batch number...">
        </div>

        @if(isset($drugs) && $drugs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Drug Name</th>
                            <th>Batch Number</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="drugTable">
                        @foreach($drugs as $drug)
                            @php
                                $expiryDate = \Carbon\Carbon::parse($drug->expiry_date);
                                $isExpiring = $expiryDate->diffInDays(\Carbon\Carbon::now()) <= 30 && $expiryDate->isFuture();
                                $isExpired = $expiryDate->isPast();
                            @endphp
                            <tr>
                                <td><strong>{{ $drug->drug_name }}</strong></td>
                                <td>{{ $drug->batch_number }}</td>
                                <td>
                                    @if($drug->quantity < 10)
                                        <span class="badge bg-danger">{{ $drug->quantity }}</span>
                                    @elseif($drug->quantity < 20)
                                        <span class="badge bg-warning">{{ $drug->quantity }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $drug->quantity }}</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($drug->unit_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($drug->quantity * $drug->unit_price, 0, ',', '.') }}</td>
                                <td>
                                    @if($isExpired)
                                        <span class="badge bg-danger">{{ $expiryDate->format('d M Y') }} (Expired)</span>
                                    @elseif($isExpiring)
                                        <span class="badge bg-warning">{{ $expiryDate->format('d M Y') }} (Soon)</span>
                                    @else
                                        {{ $expiryDate->format('d M Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if($drug->quantity === 0)
                                        <span class="badge bg-secondary">Out of Stock</span>
                                    @elseif($drug->quantity < 10)
                                        <span class="badge bg-danger">Critical</span>
                                    @elseif($drug->quantity < 20)
                                        <span class="badge bg-warning">Low</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $drug->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($isExpired || $drug->quantity === 0)
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#removeModal{{ $drug->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No drugs in inventory. <a href="{{ route('pharmacist.inventory.create') }}">Add one now</a>
            </div>
        @endif
    </div>
</div>

<!-- Edit Drug Modal -->
@if(isset($drugs))
    @foreach($drugs as $drug)
        <div class="modal fade" id="editModal{{ $drug->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit {{ $drug->drug_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('pharmacist.inventory.update', $drug) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="quantity{{ $drug->id }}" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity{{ $drug->id }}" name="quantity" value="{{ $drug->quantity }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="unit_price{{ $drug->id }}" class="form-label">Unit Price</label>
                                <input type="number" class="form-control" id="unit_price{{ $drug->id }}" name="unit_price" value="{{ $drug->unit_price }}" step="0.01" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Remove Drug Modal -->
@if(isset($drugs))
    @foreach($drugs as $drug)
        <div class="modal fade" id="removeModal{{ $drug->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Remove {{ $drug->drug_name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to remove this drug from inventory?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="POST" action="{{ route('pharmacist.inventory.destroy', $drug) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchDrugs');
    const table = document.getElementById('drugTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    }
});
</script>
@endsection
