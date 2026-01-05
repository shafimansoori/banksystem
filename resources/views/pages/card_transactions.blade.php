@extends('layouts.master')

@section('title', 'Card Transactions')

@section('content')

<div class="row">              
    <div class="col-md-12 h6">
        
    </div>      
    <div class="col-md-12">

        <div class="row"> 
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="card" style="border-radius:10px !important">
                    {{-- Transaction Table --}}
                    
                    <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Transactions 
                                        @can('add-card-transaction') 
                                            <button type="button" data-toggle="modal" data-target="#addTransactionModal" class="btn btn-primary float-right"> Add Transaction </button> 
                                        @endcan 

                                    </h4>
                                    <h6 class="card-subtitle">&nbsp;</h6>
                                
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Amount</th>
                                                    <th scope="col">Transaction Code</th>
                                                    <th scope="col">Narration</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Risk</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($cardTransactions as $cardTransaction)
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>
                                                            <div title="{{ $cardTransaction->created_at->format('l jS \\of F Y h:i:s A') }}"> 
                                                                {{ $cardTransaction->created_at->format('d/m/Y') }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $cardTransaction->card->currency->symbol." ".$cardTransaction->amount }}</td>
                                                        {{-- <td>{{ $cardTransaction->card_type->currency->symbol." ".$cardTransaction->amount }}</td> --}}
                                                        <td>{{ $cardTransaction->transaction_code }}</td>
                                                        <td>{{ ucfirst($cardTransaction->narration) }}</td>
                                                        <td>

                                                            @if($cardTransaction->type == "credit")
                                                                <span class="btn btn-sm btn-success"> <i class="mdi mdi-debug-step-into"></i> {{ ucfirst($cardTransaction->type) }} </span>
                                                            @elseif($cardTransaction->type == "debit")
                                                                <span class="btn btn-sm btn-danger"> <i class="mdi mdi-debug-step-out"></i> {{ ucfirst($cardTransaction->type) }} </span>
                                                            @else

                                                            @endif

                                                        </td>
                                                        <td>

                                                            @if($cardTransaction->status == "completed")
                                                                <span class="text-success"> <i class="mdi mdi-check-all"></i> Completed </span>
                                                            @elseif($cardTransaction->status == "pending")
                                                                <span class="text-warning"> <i class="mdi mdi-clock"></i> Pending </span>
                                                            @elseif($cardTransaction->status == "failed")
                                                                <span class="text-danger"> <i class="mdi mdi-close"></i> Failed </span>
                                                            @endif

                                                        </td>
                                                        <td>
                                                            @if($cardTransaction->risk_level == 'high')
                                                                <span class="badge badge-danger" title="{{ $cardTransaction->analysis_result }}">High</span>
                                                            @elseif($cardTransaction->risk_level == 'medium')
                                                                <span class="badge badge-warning" title="{{ $cardTransaction->analysis_result }}">Medium</span>
                                                            @elseif($cardTransaction->risk_level == 'low')
                                                                <span class="badge badge-info" title="{{ $cardTransaction->analysis_result }}">Low</span>
                                                            @else
                                                                <span class="badge badge-success">Safe</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if(count($cardTransactions) == 0)
                                                    <tr>
                                                        <td colspan="8" class="span4 text-center text-muted"> No Transaction Found</td>
                                                    </tr>
                                                @endif

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="float-right">
                                            {{ $cardTransactions->links() }}
                                        </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
     


@can('add-card-transaction')

{{-- Add The Model for the transaction adding --}}

<!-- Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">

        <form method="POST" action="{{ route('add_card_transaction') }}">
            @csrf
            <input type="hidden" value="{{ $card->id }}" name="card_id" />

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Card Transaction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-5">

                    <div class="alert alert-info">
                        <i class="mdi mdi-information"></i> Transaction code will be generated automatically
                    </div>

                    <div class="form-group">
                        <label for="transactionNarrationInput">Transaction Description *</label>
                        <input type="text" class="form-control" id="transactionNarrationInput" name="narration" required placeholder="e.g., Online shopping, Restaurant payment, etc." />
                        <small class="form-text text-muted">Describe the transaction clearly for fraud detection analysis</small>
                    </div>

                    <div class="form-group">
                        <label for="transactionAmountInput">Amount</label>
                        <input type="number" class="form-control" id="transactionAmountInput" aria-describedby="transactionAmountInputHelp" name="amount" required />
                    </div>

                    <div class="form-group">
                        <label for="transactionDateInput">Transaction Date</label>
                        <input type="date" class="form-control" id="transactionDateInput" aria-describedby="transactionDateInputHelp" name="date" required />
                    </div>


                    <label for="transactionAmountInput">Transaction Type</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="exampleRadios1" value="debit" >
                        <label class="form-check-label" for="exampleRadios1">
                            Debit
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="type" id="exampleRadios2" value="credit" checked>
                        <label class="form-check-label" for="exampleRadios2">
                            Credit
                        </label>
                    </div>

                    <p class="mt-4">Charge Transaction From</p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="balance" id="chargeRadios1" value="ledger_balance" checked>
                        <label class="form-check-label" for="chargeRadios1">
                            Ledger Balance
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="balance" id="chargeRadios2" value="available_balance">
                        <label class="form-check-label" for="chargeRadios2">
                            Available Balance
                        </label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Save </button>
                </div>
            </div>

        </form>

    </div>
</div>




@endcan

@endsection

@section('custom-script')

<script>

     $('.collapse').collapse()

</script>

@endsection
