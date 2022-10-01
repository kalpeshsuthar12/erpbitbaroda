<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class admissionprocesscourses extends Model
{
    protected $fillable=[
        'invid','courseid','subcourses','coursemode','courseprice','tax','studentsin','admissionfor','univecoursid','unoverfeess','nofstudetns',
    ];
}
