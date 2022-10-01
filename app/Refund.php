<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable=[
        'branchId','studId','paymentmode','refundamount','refuadmissionid','refunstudentanmmes','refunmobileno','refunemail','refunenrollmentsno','refuntotalfees','refuntotalpaymentreceived','refunrefundamounts','refunremaiiningamounts','refunrefundates','refunpaymodes','refunremarks','resettlementsamounts','reseetlementsbalances','recollectionsmonths','rfromsbranchs','rformsusers','ressttlemenstspaymentsid','resettlementsadmissionsids','refcourses',
    ];
}
