<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    //
    use SoftDeletes;

    public function bank()
    {
        return $this->hasOne('App\Models\Bank','id','bank_id');
    }

    public function bank_location()
    {
        return $this->hasOne('App\Models\BankLocation','id','bank_location_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currency','currency_id','id');
    }
    
    /**
     * Get currency code with fallback
     */
    public function getCurrencyCode()
    {
        return $this->currency ? $this->currency->code : 'TRY';
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
}
