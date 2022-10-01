<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    //

    protected $fillable=[
        'inviceid','reinviceid','totalamount','remainingamount','paymentreceived','paymentdate','paymentmode','bankname','chequeno','chequedate','chequetype','remarknoe','userid','studentsid','branchs','studentadmissiionstatus','receiptno','sjrecpno','mjrecpno','wgrecpno','bitolrecpno','cvrublrecpno','cvrukhrecpno','rnturecpno','manipalrecpno','studenterno','sjerno','mjerno','wgerno','cvrublerno','cvrukherno','bitolerno','rntuerno','manipalerno','installmentid','reinstallmentid','chequestatus','psusersId','chequedepositsto','buttonstatus','instid','nexamountdate','oldamount','paymentype','revisedpaymentsstatus','reviseddates','revisedpaymentsmodes','droppedstats','droppedatesa','chequeremarsk','chequedroppstatus','restudentsernos','instatus','chequescollecdates','cvstatus','transactionsids'
    ];
}
