<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstallmentFollowups extends Model
{
     protected $fillable=[
        'admissionsfrom','afollowupsstatus','afollowupsdate','afollowupsremarks','anextfollowupsdate','afollowupsby','afstatus',
    ];
}
