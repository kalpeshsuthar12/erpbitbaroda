<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class leadsfollowups extends Model
{
     protected $fillable=[
        'leadsfrom','followupstatus','takenby','flfollwpdate','flremarsk','nxtfollowupdate','userid','followupsby','fstatus'
    ];
}
