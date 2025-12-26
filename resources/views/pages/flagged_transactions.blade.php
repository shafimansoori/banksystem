@extends('layouts.master')

@section('title', 'Flagged Transactions')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-alert-octagon text-danger"></i> Suspicious Transactions
                </h4>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $stats['high'] ?? 0 }}</h3>
                                <small>High Risk</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $stats['medium'] ?? 0 }}</h3>
                                <small>Medium Risk</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $stats['low'] ?? 0 }}</h3>
                                <small>Low Risk</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <h3 class="mb-0">{{ $totalFlagged }}</h3>
                                <small>Total Flagged</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Account</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Risk Level</th>
                                <th>Analysis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <strong>{{ $transaction->user->first_name }} {{ $transaction->user->last_name }}</strong><br>
                                        <small class="text-muted">{{ $transaction->user->email }}</small>
                                    </td>
                                    <td>
                                        {{ $transaction->bank_account->name }}<br>
                                        <small class="text-muted">{{ $transaction->bank_account->number }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $transaction->transaction_code }}</span>
                                    </td>
                                    <td>{{ $transaction->narration }}</td>
                                    <td>
                                        <strong>${{ number_format($transaction->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($transaction->type == 'credit')
                                            <span class="badge badge-success">Credit</span>
                                        @else
                                            <span class="badge badge-danger">Debit</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->risk_level == 'high')
                                            <span class="badge badge-danger">High Risk</span>
                                        @elseif($transaction->risk_level == 'medium')
                                            <span class="badge badge-warning">Medium Risk</span>
                                        @elseif($transaction->risk_level == 'low')
                                            <span class="badge badge-info">Low Risk</span>
                                        @else
                                            <span class="badge badge-success">Safe</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $transaction->analysis_result }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 48px;"></i>
                                        <p class="mt-2 text-muted">No suspicious transactions detected</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
