<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pastpayments extends Model
{
     protected $fillable=[
        'pinviceid','preinviceid','ptotalamount','premainingamount','ppaymentreceived','ppaymentdate','ppaymentmode','pbankname','pchequeno','pchequedate','pchequetype','premarknoe','puserid','pstudentsid','pbranchs','pstudentadmissiionstatus','preceiptno','psjrecpno','pmjrecpno','pwgrecpno','pbitolrecpno','pcvrublrecpno','pcvrukhrecpno','prnturecpno','pmanipalrecpno','pstudenterno','psjerno','pmjerno','pwgerno','pcvrublerno','pcvrukherno','pbitolerno','prntuerno','pmanipalerno','pinstallmentid',
    ];
}
