<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bannedleads extends Model
{
     protected $fillable=[
        'bsource','bbranch','bstudentname','baddress','bemail','bphone','bcourse','bcoursesmode','blvalue','bcity','bstate','bzipcode','bdescription','bfollowupstatus','bfollowupdate','bleadstatus','bleaddate','busersid','bbyname',
    ];
}
