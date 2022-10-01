<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paymenthistory extends Model
{
    protected $fillable=[
        'paymentinvoiceid','paymentid','ppaymentmode','pbankname','pchequeno','pchequedate','pchequetype','pchequedepositto','pchequestatus',
    ];
}
