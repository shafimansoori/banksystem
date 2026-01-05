<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\CardTransaction;
use App\Models\Card;
use App\Services\TransactionAnalyzer;


class CardTransactionController extends Controller
{
    //

    public function show(Request $request,int $id){

        $card = Card::where('id', $id)
        ->where('user_id', Auth::id())
        ->first();


        if( Auth::user()?->can('view-card-transactions') ){

            $card = Card::where('id', $id)->first();

        }


        if(!empty($card)){

            $cardTransactions = CardTransaction::with('card.card_type','card.currency')
            ->where('user_id',Auth::id())
            ->where('card_id',$id)
            ->paginate(20);

            if( Auth::user()?->can('view-card-transactions') ){

                $cardTransactions = CardTransaction::with('card.card_type','card.currency')
                ->where('card_id',$id)
                ->paginate(20);

            }

            return view('pages.card_transactions', compact('cardTransactions', 'card'));

        }

        return redirect()->route('cards')->with('error', 'Card Can\'t Be Found. Please Try Again.');

    }



    function storeTransaction(Request $request){

        DB::beginTransaction();
        try{

            $card = Card::withTrashed()->find($request->card_id);

            if(!$card){
                DB::rollBack();
                return redirect()->route('cards')->with('error', 'Card not found.');
            }

            // Check balance for debit transactions
            if($request->type == "debit"){
                $currentBalance = $request->balance == "ledger_balance"
                    ? $card->ledger_balance
                    : $card->available_balance;

                if($currentBalance < $request->amount){
                    DB::rollBack();
                    $balanceType = $request->balance == "ledger_balance" ? "Ledger" : "Available";
                    return redirect()->route('card_transactions', $card->id)
                        ->with('error', "Insufficient {$balanceType} Balance. Current balance: $" . number_format($currentBalance, 2) . ", Requested: $" . number_format($request->amount, 2));
                }
            }

            // Update balance
            if($request->type == "credit"){

                if($request->balance == "ledger_balance"){
                    $card->ledger_balance = $card->ledger_balance + $request->amount;
                }else{
                    $card->available_balance = $card->available_balance + $request->amount;
                }

            }else{

                if($request->balance == "ledger_balance"){
                    $card->ledger_balance = $card->ledger_balance - $request->amount;
                }else{
                    $card->available_balance = $card->available_balance - $request->amount;
                }

            }

            $card->save();

            // Generate unique transaction code
            $transactionCode = 'CARD-' . strtoupper(uniqid()) . '-' . time();

            // Analyze transaction using AI/Text Mining
            $analyzer = new TransactionAnalyzer();
            $analysis = $analyzer->analyzeTransaction(
                $request->narration ?? '',
                $request->amount,
                $request->type
            );

            $cardTransaction = new CardTransaction();
            $cardTransaction->transaction_code = $transactionCode;
            $cardTransaction->narration = $request->narration;
            $cardTransaction->amount = $request->amount;
            $cardTransaction->type = $request->type;
            $cardTransaction->card_id = $request->card_id;
            $cardTransaction->status = 'completed'; // Always completed for new transactions
            $cardTransaction->user_id = $card->user_id;
            $cardTransaction->risk_level = $analysis['risk_level'];
            $cardTransaction->analysis_result = $analysis['analysis'];
            $cardTransaction->is_flagged = $analysis['is_flagged'];
            $cardTransaction->created_at = Carbon::parse($request->date);
            $cardTransaction->updated_at = Carbon::parse($request->date);
            $cardTransaction->save();

            DB::commit();

            return redirect()->route('card_transactions', $card->id)->with('success', 'Card Transaction Addition Successfully.');

        }catch(\Exception $ex){

            DB::rollBack();
            Log::error('Card Transaction Failed: ' . $ex->getMessage());

            $errorMessage = 'Card Transaction Addition Failed: ';

            // Specific error messages
            if(str_contains($ex->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'Transaction code already exists.';
            } elseif(str_contains($ex->getMessage(), 'cannot be null')) {
                $errorMessage .= 'Required fields are missing.';
            } else {
                $errorMessage .= $ex->getMessage();
            }

            return redirect()->route('card_transactions', $request->card_id ?? null)
                ->with('error', $errorMessage);

        }


    }



}
