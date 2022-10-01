<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class quotation_courses_details extends Model
{
    protected $fillable=[
        'companyquotationid','compcourse','compspecializations','compcoursemode','compcoursefees','compnofstudents',
    ];
}
