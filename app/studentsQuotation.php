<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class studentsQuotation extends Model
{
    protected $fillable=[
        'studentscategorys','quotationsdates','quotationsduedates','studentsocompanyname','contactperson','scemail','scphones','scwhatsappno','scsubtotal','scdiscounttypes','scdiscountstotals','scdiscountstotals','ssgstamounts','scgstamounts','scfinaltotal','scbranch','scquonos','sjqnos','mjqnos','wgqnos','elqnos','bitolqnos','cvrublqnos','cvrukhqnos','rntuqnos','manipalnos','squsersid','subranchse','leadids','qleaddates','scaddress','scgstnos',
    ];
}
