<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncentiveReleasePayments extends Model
{
        protected $fillable=['incentcollections','mincentivs','payableincentivespayments','remainingincentives','incpaymentsmodes','incentivespaymentsdates','iusersids','ibranchs','mothsof',
    ];
}
