<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cheque_followups extends Model
{
     protected $fillable=[
        'cadmissionsfrom','cafollowupsstatus','cafollowupsdate','cafollowupsremarks','canextfollowupsdate','cafollowupsby','cafstatus',
    ];
}
