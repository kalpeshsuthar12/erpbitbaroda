<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    //
  //  use HasFactory;

     protected $fillable = [
        "user_id",
        "driver",
        "host",
        "port",
        "encryption",
        "user_name" ,
        "password",
        "sender_name",
        "sender_email"
    ];
}
