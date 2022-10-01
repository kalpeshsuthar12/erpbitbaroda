<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersOfficialsTimingsDetails extends Model
{
     protected $fillable=[
        'usersdetailsid','usersdetailsbranchs','usersdetailsmodes','usersdetailsdays','usersdetailsintimings','usersdetailsouttimings','salarys'
    ];
}
