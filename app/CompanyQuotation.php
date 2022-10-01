<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyQuotation extends Model
{
    protected $fillable=[
        'quotatdate','quotatduedate','cname','ccontactperson','cphoneno','cwhatsappno','cemails','caddress','cgstnos','quotationno','quotenos','csubtotal','cdiscountypes','cdiscounts','ctotal','cgsttax','ctaxamounts','cfinaltotals','leaddates','user_id',
    ];
}
