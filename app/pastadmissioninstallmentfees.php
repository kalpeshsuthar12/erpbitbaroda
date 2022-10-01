<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pastadmissioninstallmentfees extends Model
{
    protected $fillable=[
        'pinvoid','pinvoicedate','pinstallmentamount','ppendinamount','pstatus',
    ];
}
