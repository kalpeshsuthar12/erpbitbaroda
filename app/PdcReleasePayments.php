<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PdcReleasePayments extends Model
{
    protected $fillable=[
        'pdcollectionss','clerchcollections','cheincentives','pdctotalincentives','pdcpaidincentives','pdcpayableincentives','pdcremaininvcentives','pdcspmodes','pdcpaymtnsdates','piusersids','pibranchs','pmothsof',
    ];
}
