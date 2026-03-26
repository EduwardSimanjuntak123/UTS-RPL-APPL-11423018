@if($payments->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>
                            <strong>#{{ $payment->transaction_id }}</strong>
                        </td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                        </td>
                        <td>
                            @if($payment->status === 'completed')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Completed
                                </span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning">
                                    <i class="bi bi-hourglass-split"></i> Pending
                                </span>
                            @elseif($payment->status === 'failed')
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> Failed
                                </span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $payment->id }}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Payment Detail Modals -->
    @foreach($payments as $payment)
        <div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" value="#{{ $payment->transaction_id }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($payment->amount, 0, ',', '.') }}" disabled>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Method</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($payment->payment_method) }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($payment->status) }}" disabled>
                                </div>
                            </div>
                        </div>
                        @if($payment->notes)
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="2" disabled>{{ $payment->notes }}</textarea>
                            </div>
                        @endif
                        <small class="text-muted">
                            Created: {{ $payment->created_at->format('d M Y H:i') }}
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @if($payment->status === 'failed')
                            <button type="button" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise"></i> Retry Payment
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No payments found in this category.
    </div>
@endif
