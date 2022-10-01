<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefundsSettlements extends Model
{
    protected $fillable=[
        'rspaymentsdate','rsstudentsnames','rsenrollmentno','rscourse','rspayablefees','rsrefundamounts','rsbalance','rscmonths','rsbranchs','rsusers','rsstudentadmissionids','rspaymentids', ];
}
