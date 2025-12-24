<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    //

    public function bank_account()
    {
        return $this->hasOne('App\Models\BankAccount','id','bank_account_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo('App\Models\BankAccount','bank_account_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }


}
