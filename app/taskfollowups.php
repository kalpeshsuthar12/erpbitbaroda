<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class taskfollowups extends Model
{
    protected $fillable = ['tasksid','taskstatus','taskfoldate','tfremarks','tasknxtfoldate','tfollbys','fstatus'];
}
