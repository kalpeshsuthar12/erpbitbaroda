<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pastadmissioncourse extends Model
{
    protected $fillable=[
        'pinvid','pcourseid','psubcourses','pcoursemode','pcourseprice','ptax','pstudentsin','padmissionfor','punivecoursid','punoverfeess',
    ];
}
