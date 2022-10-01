<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave_Management extends Model
{
    protected $fillable = ['leavuserid','leavesdate','userstotalleave'];
}
