@if($prescriptions->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Patient</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $prescription)
                    <tr>
                        <td><strong>{{ $prescription->medication }}</strong></td>
                        <td>{{ $prescription->patient->name ?? 'N/A' }}</td>
                        <td>{{ $prescription->dosage }}</td>
                        <td>{{ $prescription->frequency }}</td>
                        <td>{{ $prescription->duration }} days</td>
                        <td>
                            @if($prescription->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($prescription->status === 'completed')
                                <span class="badge bg-primary">Completed</span>
                            @elseif($prescription->status === 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ $prescription->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('doctor.prescriptions.show', $prescription) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('doctor.prescriptions.edit', $prescription) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No prescriptions found.
    </div>
@endif
