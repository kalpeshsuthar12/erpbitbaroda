<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class readmissioninstallmentfees extends Model
{
    protected $fillable=[
        'reinvoid','reinvoicedate','reinstallmentamount','rependinamount','restatus',
    ];
}
