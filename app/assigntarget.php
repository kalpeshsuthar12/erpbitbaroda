<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class assigntarget extends Model
{
      protected $fillable=[
        'targtname','tmonth','bycb','usercategory','tassignuser','targetamount','tbranch','incentivepercent','startsdates','enddates','affiliatescateogry',
    ];
}
