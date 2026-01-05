<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Bank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TransactionAnalyzer;
use App\Models\CardTransaction;


class BankAccountController extends Controller
{

    public function index(){

        $bankAccounts = BankAccount::with('bank','bank_location')
        ->orderBy('id','DESC')
        ->where('user_id',Auth::id())->paginate(9);

        if(Auth::user()?->can('view-all-accounts')){
            $bankAccounts = BankAccount::withTrashed()->with('bank','bank_location')
            ->orderBy('id','DESC')->paginate(9);
        }

        $users = User::role('Customer')->get();
        $banks = Bank::with('location')->get();

        return view('pages.accounts', compact('bankAccounts','users','banks'));

    }

    public function transactions(Request $request,$id){

        $bankAccount = BankAccount::with('bank_location.currency')->withTrashed()
        ->where('id',$id)
        ->where('user_id',Auth::id())
        ->first();

        if(Auth::user()?->can('view-bank-transactions')){

            $bankAccount = BankAccount::with('bank_location.currency')->withTrashed()
            ->find($id);

        }


        if(!empty($bankAccount)){

            $bankTransactions = BankTransaction::where('user_id',Auth::id())
            ->where('bank_account_id',$id)
            ->orderBy('id','DESC')
            ->paginate(20);

            if(Auth::user()?->can('view-bank-transactions')){

                $bankTransactions = BankTransaction::where('bank_account_id',$id)
                ->orderBy('id','DESC')
                ->paginate(20);


            }   


            return view('pages.transections', compact('bankAccount', 'bankTransactions'));

        }

        return redirect()->route('accounts')->with('error', 'Bank Account Transaction Can\'t Be Found. Please Try Again.');

    }


    public function all_transactions(Request $request){

        $bankTransactions = BankTransaction::with('bank_account','bank_account.bank','bank_account.bank_location')
        ->where('user_id',Auth::id())
        ->orderBy('id','DESC')
        ->paginate(20);

        if(Auth::user()?->can('view-all-transactions')){

            $bankTransactions = BankTransaction::with('bank_account','bank_account.bank','bank_account.bank_location')
            ->orderBy('id','DESC')
            ->paginate(20);

        }



        return view('pages.all_transactions', compact('bankTransactions'));

    }


    function storeTransaction(Request $request){

        DB::beginTransaction();
        try{

            $bankAccount = BankAccount::withTrashed()->find($request->bank_account_id);

            if(!$bankAccount){
                DB::rollBack();
                return redirect()->route('accounts')->with('error', 'Bank account not found.');
            }

            // Check balance for debit transactions
            if($request->type === "debit"){
                $currentBalance = $request->balance === "ledger_balance"
                    ? $bankAccount->ledger_balance
                    : $bankAccount->available_balance;

                if($currentBalance < $request->amount){
                    DB::rollBack();
                    $balanceType = $request->balance === "ledger_balance" ? "Ledger" : "Available";
                    return redirect()->route('account_history', $bankAccount->id)
                        ->with('error', "Insufficient {$balanceType} Balance. Current balance: $" . number_format($currentBalance, 2) . ", Requested: $" . number_format($request->amount, 2));
                }
            }

            // Update balance
            if($request->type === "credit"){

                if($request->balance === "ledger_balance"){
                    $bankAccount->ledger_balance = $bankAccount->ledger_balance + $request->amount;
                }else{
                    $bankAccount->available_balance = $bankAccount->available_balance + $request->amount;
                }

            }else{

                if($request->balance === "ledger_balance"){
                    $bankAccount->ledger_balance = $bankAccount->ledger_balance - $request->amount;
                }else{
                    $bankAccount->available_balance = $bankAccount->available_balance - $request->amount;
                }

            }

            $bankAccount->save();

            // Generate unique transaction code
            $transactionCode = 'BANK-' . strtoupper(uniqid()) . '-' . time();

            // Analyze transaction using AI/Text Mining
            $analyzer = new TransactionAnalyzer();
            $analysis = $analyzer->analyzeTransaction(
                $request->narration ?? '',
                $request->amount,
                $request->type
            );

            $bankTransaction = new BankTransaction();
            $bankTransaction->transaction_code = $transactionCode;
            $bankTransaction->narration = $request->narration;
            $bankTransaction->amount = $request->amount;
            $bankTransaction->type = $request->type;
            $bankTransaction->bank_account_id = $request->bank_account_id;
            $bankTransaction->status = 'successful'; // Always successful for new transactions
            $bankTransaction->user_id = $bankAccount->user_id;
            $bankTransaction->risk_level = $analysis['risk_level'];
            $bankTransaction->analysis_result = $analysis['analysis'];
            $bankTransaction->is_flagged = $analysis['is_flagged'];
            $bankTransaction->created_at = Carbon::parse($request->date);
            $bankTransaction->updated_at = Carbon::parse($request->date);
            $bankTransaction->save();

            DB::commit();

            return redirect()->route('account_history', $bankAccount->id)->with('success', 'Bank Transaction Addition Successfully.');

        }catch(\Exception $ex){

            DB::rollBack();
            Log::error('Bank Transaction Failed: ' . $ex->getMessage());

            $errorMessage = 'Bank Transaction Addition Failed: ';

            // Specific error messages
            if(str_contains($ex->getMessage(), 'Duplicate entry')) {
                $errorMessage .= 'Transaction code already exists.';
            } elseif(str_contains($ex->getMessage(), 'cannot be null')) {
                $errorMessage .= 'Required fields are missing.';
            } else {
                $errorMessage .= $ex->getMessage();
            }

            return redirect()->route('account_history', $request->bank_account_id ?? null)
                ->with('error', $errorMessage);

        }


    }

    function store(Request $request){

        DB::beginTransaction();

        try{

            if( Auth::user()?->can('add-account') ){

                $bankAccount = new BankAccount();
                $bankAccount->name = $request->name;
                $bankAccount->number = $request->number;
                $bankAccount->user_id = $request->user_id;
                $bankAccount->available_balance = $request->available_balance;
                $bankAccount->ledger_balance = $request->ledger_balance;
                $bankAccount->bank_id = $request->bank_id;
                $bankAccount->bank_location_id = $request->bank_location_id;
                $bankAccount->save();

                DB::commit();
                return redirect()->route('accounts')->with('success', 'Bank Account Addition Successful');

            }

            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Addition Failed. UnAuthorized Action Failed');

        }catch(\Exception $ex){

            Log::info($ex->getMessage());
            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Addition Failed. Please Try Again Later');

        }

    }


    function update(Request $request,int $id){

        DB::beginTransaction();

        try{

            if( Auth::user()?->can('edit-account') ){

                $bankAccount = BankAccount::findOrfail($id);
                $bankAccount->name = $request->name;
                $bankAccount->number = $request->number;
                $bankAccount->available_balance = $request->available_balance;
                $bankAccount->ledger_balance = $request->ledger_balance;
                $bankAccount->bank_id = $request->bank_id;
                $bankAccount->bank_location_id = $request->bank_location_id;
                $bankAccount->save();

                DB::commit();
                return redirect()->route('accounts')->with('success', 'Bank Account Updated Successfully');

            }

            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Update Failed. UnAuthorized Action Failed');

        }catch(\Exception $ex){

            Log::info($ex->getMessage());
            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Update Failed. Please Try Again Later');

        }

    }

    function destory(Request $request,int $id){

        DB::beginTransaction();

        try{

            if( Auth::user()?->can('delete-account') ){

                $bankAccount = BankAccount::findOrfail($id);
                $bankAccount->delete();

                DB::commit();
                return redirect()->route('accounts')->with('success', 'Bank Account Deletion Successfully');

            }

            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Deletion Failed. UnAuthorized Action Failed');

        }catch(\Exception $ex){

            Log::info($ex->getMessage());
            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Deletion Failed. Please Try Again Later');

        }

    }

    function restore(Request $request,int $id){

        DB::beginTransaction();

        try{

            if( Auth::user()?->can('restore-account') ){

                $bankAccount = BankAccount::withTrashed()->findOrfail($id);
                $bankAccount->restore();

                DB::commit();
                return redirect()->route('accounts')->with('success', 'Bank Account Restore Successfully');

            }

            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Restore Failed. UnAuthorized Action Failed');

        }catch(\Exception $ex){

            Log::info($ex->getMessage());
            DB::rollBack();
            return redirect()->route('accounts')->with('error', 'Bank Account Restore Failed. Please Try Again Later');

        }

    }

    public function flaggedTransactions()
    {
        // Get flagged bank transactions
        $bankTransactions = BankTransaction::with('user', 'bank_account')
            ->where('is_flagged', true)
            ->orderBy('risk_level', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get flagged card transactions (with nested relations)
        $cardTransactions = CardTransaction::with(['user', 'card.card_type', 'card.currency'])
            ->where('is_flagged', true)
            ->orderBy('risk_level', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Merge and sort all transactions
        $allTransactions = $bankTransactions->merge($cardTransactions)
            ->sortByDesc(function($transaction) {
                $riskOrder = ['high' => 3, 'medium' => 2, 'low' => 1, 'safe' => 0];
                return $riskOrder[$transaction->risk_level] * 1000000 + strtotime($transaction->created_at);
            });

        // Paginate manually
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allTransactions->forPage($currentPage, $perPage),
            $allTransactions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Calculate statistics
        $totalFlagged = BankTransaction::where('is_flagged', true)->count() +
                       CardTransaction::where('is_flagged', true)->count();

        $stats = [
            'high' => BankTransaction::where('risk_level', 'high')->count() +
                     CardTransaction::where('risk_level', 'high')->count(),
            'medium' => BankTransaction::where('risk_level', 'medium')->count() +
                       CardTransaction::where('risk_level', 'medium')->count(),
            'low' => BankTransaction::where('risk_level', 'low')->count() +
                    CardTransaction::where('risk_level', 'low')->count(),
        ];

        return view('pages.flagged_transactions', compact('transactions', 'totalFlagged', 'stats'));
    }

}
