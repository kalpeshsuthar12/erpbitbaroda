<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class admissionprocessinstallmentfees extends Model
{
    protected $fillable=[
        'invoid','invoicedate','installmentamount','pendinamount','status','bstatus',
    ];
}
