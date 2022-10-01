<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class assignbatch extends Model
{
     protected $fillable=[
        'startdate','enddate','jointo','assignstatus','faculty','leccategory','ftransferfroms','transfdates','bdurationsdays','batchtimes','days','classurls','assignstatus','batchsnos','bsjno','bmjno','bwgno','belno',
    ];
}
