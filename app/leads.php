<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class leads extends Model
{
    //

    protected $fillable=[
        'source','branch','tobranchs','studentname','address','email','phone','whatsappno','course','coursesmode','lvalue','city','state','zipcode','followupstatus','followupdate','user_id','leaddate','reffrom','refname','refassignto','walkedinstatus','institutions','transferfrom','description','affiliatescategorynames','transferbranch','transferto','transferdate',
    ];
}
