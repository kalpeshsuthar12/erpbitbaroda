<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class task extends Model
{
    protected $fillable=[
        'taskname','tassugnto','tassignfrom','startdate','duedate','tasksfiles','tasdescription','userId','status',
    ];
}
