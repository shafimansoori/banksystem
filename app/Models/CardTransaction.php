<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'card_id',
        'transaction_code',
        'narration',
        'type',
        'amount',
        'status',
        'risk_level',
        'analysis_result',
        'is_flagged',
        'created_at',
        'updated_at',
    ];

    public function card()
    {
        return $this->hasOne('App\Models\Card','id','card_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
}
