<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invoicesinstallmentfees extends Model
{
    //

    protected $fillable=[
        'invoid','invoicedate','installmentamount','pendinamount',
    ];
}
