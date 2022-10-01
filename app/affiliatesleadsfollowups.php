<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class affiliatesleadsfollowups extends Model
{
    protected $fillable=[
        'afleadsfrom','affollowupstatus','aftakenby','affollowupdates','affollowupremarks','afnextsfollowupdates','affollupsby','afstatus','afuserid',
    ];
}
