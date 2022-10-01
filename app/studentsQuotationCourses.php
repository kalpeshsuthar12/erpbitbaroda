<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class studentsQuotationCourses extends Model
{
    protected $fillable=[
        'stucompyid','studecompcourse','studecompspecializations','studecoursemode','studecoursefeess','compnystudents',
    ];
}
