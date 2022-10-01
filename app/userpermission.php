<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class userpermission extends Model
{
    protected $fillable = 
    [
        'usersid','coursecategory','coursesubcategory','course','leads','invoice','admission','directadmission','source','assigntarget','followup','generatepaymentreciept',
    ];
}
