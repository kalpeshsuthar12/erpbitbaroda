<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChequeAgainstMoney extends Model
{
    protected $fillable = ['cacpid','cacpaymodes','cactotalamounts','cacpayableamounts','cacremainingamounts','cacbanknames','cacchequenos','cacchequedates','cacchequtyoe','cacpaymentdates','cacnextamountdates','cacremarks'];
}
