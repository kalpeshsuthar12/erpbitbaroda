<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounting extends Model
{
     protected $fillable=[
        'paymentids','abranchs','ppaymentmodes','clearstatus','ppcollections','caarryforwardamount','carrynextdatews','ostatus','carryforolddates','bstatys','clearamountdate','clrstatus'
    ];
}
