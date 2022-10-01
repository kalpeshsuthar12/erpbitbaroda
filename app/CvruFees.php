<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CvruFees extends Model
{
    protected $fillable=[
        'studentid','cpaymentdate','sverno','cvrufees','bitfees','studentsreadmissionid','studentsadmissionid','studentsnames','coursenames','admissionsfors','totalfees','payablefees','tbalancefees','universityfees','releeaseddates','preceiptnos',
    ];
}
