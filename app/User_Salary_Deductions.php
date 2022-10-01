<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_Salary_Deductions extends Model
{
    protected $fillable = ['salsusersid','salssalarysid','salsworkingsalarys','salsfinalsalarys','totalrealeasesalary','salspaidsalarys','salspendingsalarys','salspaymentdate','salspaymoddes','schqdates','schqno','sbanknanmes','scheqof','schqamounts','smonthsdatas'];
}
