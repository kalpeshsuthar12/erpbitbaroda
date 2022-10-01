<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPunching extends Model
{
    //

    protected $fillable = ['pusersid','title','punch_in','punch_out','puncdates'];
}
