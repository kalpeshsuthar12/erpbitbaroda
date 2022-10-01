<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Haruncpi\LaravelUserActivity\Traits\Loggable;

class User extends Authenticatable
{
    use Notifiable;
    use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','personalemail','password','mobileno','uwhatsappnos','usercategory','branchs','mdaysu','mintimings','mouttimings','baddress','usource','uaffiliatescreateddates','ufrombranchs','utobranchs','uaffitraining','uafficategory','uaffinames','ucompanyname','ucity','ustate','utstatus','sepass','agreementfile','pancardfile','acardsfiles','profilepic','gstcertificatefile','compabyregisterafile','resumefile','btnstatus','tstatus','rstatus','ustusdentsadmssionsids','employeenos','enos','uaddress','cmpnames','usalarys','udeposite','udestartsdates','udeendsdates','userstatus','apleaves','paidsleaves','expcategories','ujoiningdate','acccheckbox','accbanksremarks','affileadsid','uremarks','udesignations',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
