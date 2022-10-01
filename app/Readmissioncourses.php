<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Readmissioncourses extends Model
{
     protected $fillable=[
        'reinvid','recourseid','resubcourses','recoursemode','recourseprice','retax','restudentsin','readmissionfor','reunivecoursid','reunoverfeess',
    ];
}
