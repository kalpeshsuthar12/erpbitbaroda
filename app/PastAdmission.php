<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PastAdmission extends Model
{
    protected $fillable=[
        'pstudentname','pfnames','pmnames','psdobs','psemails','psphone','pswhatsappno','psadate','psbrnanch','pstobranches','psuniversities','pserno','psstreet','pscity','psstate','pszipcode','pspreferrabbletime','prefeassignto','preferfrom','prefername','psremarknotes','pIbranchs','pInvoiceno','pIsjno','pImjno','pIwgno','pIbitolno','pIcvrublno','pIcvrukhno','pIrntuno','pImanipalno','pinvdate','pduedate','pipaymentmodes','pidiscounttypes','pisubtotal','pdiscounttotal','pitax','pinvtotal','puserid','pgstprices','padmissionstatus','pstatus','poldtotalpice','pdiscount',
    ];
}
