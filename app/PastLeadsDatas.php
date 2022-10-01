<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PastLeadsDatas extends Model
{
    protected $fillable = [
        'ptsource','oldid','ptleadsdates','ptoldleadsdates','ptassignedto','ptbranch','pttobranchs','ptinstitutions','ptaffiliatescategorynames','ptstudentname','ptaddress','ptemail','ptphone','ptwhatsappno','ptcourse','ptcoursesmode','ptlvalue','ptreffrom','ptrefname','ptrefassignto','ptcity','ptstate','ptzipcode','ptdescription','ptleadstatus','ptleadduration','ptleaddate','ptconversationstatus','ptwalkedinstatus','ptuser_id','pttransferfrom','pttransferbranch','ptransferto','ptransferdate'
    ];
}
