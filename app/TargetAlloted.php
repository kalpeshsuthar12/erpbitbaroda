<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetAlloted extends Model
{
    protected $fillable=[
        'targetuserid','basetarget','targetamounts','totaltargets','incentive','statsus',
    ];
}
