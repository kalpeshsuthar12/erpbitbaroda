<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class reference extends Model
{
    protected $fillable=[
        'referencefrom','referencename','assignto','rphone','courses','fees','incentiveby','incentive','iamounts','paynmentdate','paymentmode','status','userid','descriptiions','abranchs','rwhatsapp',
    ];
}
