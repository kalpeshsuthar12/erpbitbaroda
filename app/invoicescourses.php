<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invoicescourses extends Model
{
    //
      protected $fillable=[
        'invid','courseid','courseprice','coursemode','tax',
    ];
}
