<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationInvoicesCourses extends Model
{
     protected $fillable=[
        'invstucompyid','invstudecompcourse','invstudecompspecializations','invstudecoursemode','invstudecoursefeess','invcompnystudents',
    ];
}
