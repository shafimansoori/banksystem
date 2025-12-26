@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

<div class="row">
    <div class="col-md-12 h6">
        Banks
    </div>
    <div class="col-md-12">
        <div class="row">
        @foreach ($bankAccounts as $bankAccount)

            <div class="col-sm-6 col-md-6 col-lg-4">
                <div class="card" style="border-radius:10px !important">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-7">
                                <div>Bank Name.</div>
                                <div class="h6">{{ $bankAccount->bank->name }} {{ $bankAccount->bank_location->name }}</div>

                                <br/>

                                <div>Account Name.</div>
                                <div class="h6">{{ $bankAccount->name }}</div>

                                <div>Account No.</div>
                                <div class="h6">{{ $bankAccount->number }}</div>

                            </div>
                            <div class="col-sm-12 col-md-5 text-right">
                                @if(!empty($bankAccount->bank->picture))
                                    <img src="{{ $bankAccount->bank->picture }}" alt="{{ $bankAccount->bank->name }}" class="rounded-circle" width="50" height="50">
                                @endif

                                <br/>
                                <br/>

                                <div>Ledger Balance.</div>
                                <div class="h6 text-muted">{{ $bankAccount->bank_location->currency->symbol }} {{ $bankAccount->ledger_balance }}</div>

                                <div>Available Balance.</div>
                                <div class="h6">{{ $bankAccount->bank_location->currency->symbol }} {{ $bankAccount->available_balance }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- Sales chart -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title">Deposit</h4>
                        <h5 class="card-subtitle">Overview of Last 7days</h5>
                    </div>
                </div>
                <div class="row">
                    <!-- column -->
                    <div class="col-lg-12">
                        <canvas id="depositsChart" width="400" height="200"></canvas>
                    </div>
                    <!-- column -->
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Expenses</h4>
                <h5 class="card-subtitle">Overview of Last 7days</h5>
                <canvas id="expensesChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- Sales chart -->
<!-- ============================================================== -->


@endsection

@section('custom-script')

<script>

function loadDepositChart(){
    var ctx = document.getElementById('depositsChart').getContext('2d');

    var labels = [
        @foreach($bankDepositDates as $bankDepositDate)
            '{{$bankDepositDate->format('D d M')}}',
        @endforeach
    ];

    var data = [
        @foreach($bankDepositAmounts as $bankDepositAmount)
            {{$bankDepositAmount}},
        @endforeach
    ];

    // Check if no data
    if (labels.length === 0) {
        labels = ['No Data'];
        data = [0];
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Deposit Amount',
                data: data,
                fill: true,
                backgroundColor: 'rgba(254, 121, 79, 0.2)',
                borderColor: 'rgb(254, 121, 79)',
                tension: 0.1,
                pointBackgroundColor: 'rgb(254, 121, 79)',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
}


function loadExpensesChart(){
    var ctx = document.getElementById('expensesChart').getContext('2d');

    var labels = [
        @foreach($bankExpensesDates as $bankExpensesDate)
            '{{$bankExpensesDate->format('D d M')}}',
        @endforeach
    ];

    var data = [
        @foreach($bankExpensesAmounts as $bankExpensesAmount)
            {{$bankExpensesAmount}},
        @endforeach
    ];

    // Check if no data
    if (labels.length === 0) {
        labels = ['No Data'];
        data = [0];
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Expense Amount',
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    loadDepositChart();
    loadExpensesChart();
});

</script>

@endsection
