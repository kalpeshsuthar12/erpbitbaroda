<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationInvoices extends Model
{
    protected $fillable=[
        'invstudentscategorys','quotationsid','invdates','invstduedates','invstudentsocompanyname','invcontactperson','invscemail','invscphones','invscwhatsappno','invscsubtotal','invscdiscounttypes','invscdiscountstotals','invscgstamounts','invscfinaltotal','invscbranch','invscquonos','invsjqnos','invmjqnos','invbitolqnos','invelqnos','invwgqnos','invcvrublqnos','invcvrukhqnos','invrntuqnos','invmanipalnos',
    ];
}
