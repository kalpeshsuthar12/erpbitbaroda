<?php

namespace App\Http\Controllers;
use App\assigntarget;
use App\TargetAlloted;
use App\leads;
use App\leadsfollowups;
use App\followup;
use App\Branch;
use App\invoices;
use App\payment;
use App\admissionprocess;
use App\termsandconditions;
use App\universititiesfeeslist;
use App\User;
use App\CvruFees;
use App\Refund;
use App\sapaccounting;
use Auth;
use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function pastadmission()
    {
        return view('pastadminhome');
    }
    
    public function studentuse(assigntarget $assigntarget,leads $leads,payment $payment)
    {
       


        return view('studenthome');
    }  
    
    
     public function index(Request $request)
    {
           
      
         $UserBranch = Auth::user()->branchs;
        $currentMonth = date('m');
            $almarkusrt = User::where('usercategory','Marketing')->get();
            $branchalldata = Branch::all(); 
          /*  $sum = payment::sum('paymentreceived');*/
            $leadsdatas = leads::where('tobranchs',$UserBranch)->whereMonth('leaddate',$currentMonth)->count();
         
              $conversionstatus = admissionprocess::whereMonth('sadate', $currentMonth)->count();
               $invodata = admissionprocess::whereMonth('sadate', $currentMonth)->sum('invtotal');

               


        // $totaladmission = admissionprocess::where('stobranches',$UserBranch)->whereMonth('sadate', $currentMonth)->count();
         
         $totaladmission = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$UserBranch)->where('payments.studenterno','!=',NULl)->whereMonth('sadate', $currentMonth)->count();
         
         $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$UserBranch)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',0)->first();

         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch','BITSJ')->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
        
            if($tdata)
            {
                $targetdata = $tdata->totaltargets;
            }
            elseif($ntdadat)
            {
               $targetdata = $ntdadat->totaltargets;
            }

            else
            {
                $targetdata = 0;
            }

          
               
                                            $refuncalculcation = DB::table('refunds')->where('rfromsbranchs',$UserBranch)->whereMonth('recollectionsmonths',$currentMonth)->get()->sum('resettlementsamounts');
                                                
                                                
                                               $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$UserBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

                                                            $ss_sum = 0;
                                                            $ins_sum = 0;
                                                            $cvru_sum = 0;
                                                             $sap_sum = 0;
                                                            foreach($namesfinds as $students)
                                                            {
                                                                     $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $getcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $cvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                       $cvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();

                                                                        $sapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $getsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                   if($getcvrusrefunds)
                                                                   {


                                                                    if($cvrufeessdetaiks)
                                                                    {

                                                                        $cvru_sum  +=  $cvrusfesscalcultations->cvrufees;
                                                                    }



                                                                           
                                                                            
                                                                

                                                                  }

                                                                   if($getsapfeesrefunds)
                                                                    {

                                                                         if($sapfeesdetails)
                                                                        {

                                                                        $sap_sum  +=  $sapfeesdetails->sapfees;
                                                                                                                                           
                                                                        }

                                                                    }

                                                                  if($getrefunds)
                                                                    {
                                                                         if($getrefunds->refunenrollmentsno != null)
                                                                            {

                                                                            }


                                                                    }
                                                                    else
                                                                    {
                                                                        if($students->studenterno != null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }

                                                                        else if($students->studenterno == null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                             else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            }


                                                            $freshadmissions = $ss_sum + $ins_sum;


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$UserBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

                                                            $rins_sum  = 0;
                                                            $rcvru_sum = 0;
                                                            $rsap_sum = 0;
                                                            foreach($reinvoicesdata as $students)
                                                            {
                                                                     $regetrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $regetcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $recvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                        $recvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();


                                                                         $resapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $regetsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                   if($regetcvrusrefunds)
                                                                   {


                                                                    if($recvrufeessdetaiks)
                                                                    {

                                                                        $rcvru_sum  +=  $recvrusfesscalcultations->cvrufees;
                                                                    }



                                                                           
                                                                            
                                                                

                                                                  }

                                                                    if($regetsapfeesrefunds)
                                                                    {

                                                                         if($resapfeesdetails)
                                                                        {

                                                                        $rsap_sum  +=  $sapfeesdetails->sapfees;
                                                                                                                                           
                                                                        }

                                                                    }


                                                                  if($getrefunds)
                                                                    {
                                                                         if($getrefunds->refunenrollmentsno != null)
                                                                            {

                                                                            }


                                                                    }
                                                                    else
                                                                    {
                                                                        

                                                                        if($students->reinviceid)
                                                                        {
                                                                            if($recvrufeessdetaiks)
                                                                            {
                                                                                if($recvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $rins_sum  += abs($recvrufeessdetaiks->cvrufees - $students->paymentreceived - $students->rgstprices);
                                                                                }
                                                                                else if($recvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                                }
                                                                            }

                                                                            else if($resapfeesdetails)
                                                                            {

                                                                                 if($resapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $rins_sum  += abs($resapfeesdetails->sapfees - $students->paymentreceived - $students->rgstprices);
                                                                                }
                                                                                else if($resapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                                }

                                                                            }

                                                                            else
                                                                            {
                                                                                 $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            }


                                                    //dd($cvrucoles);

                                                /*$bitcollection = $ernrollmentfees + $installmentfees + $reernrollmentfees;
                                                $cvrucolles = $cvrucoles + $recvrucoles;*/
                                     
                               $totalpaymentreceives = $rins_sum + $freshadmissions;

                                $ovalcal = $refuncalculcation;
                               
                                
                                             $paumentdats = $totalpaymentreceives;              
                                    



                
        $stshid = assigntarget::where('tbranch','BITSJ')->whereMonth('enddates',$currentMonth)->pluck('id');
          if($targetdata < $paumentdats)
         {
                 foreach($stshid as $tid)
                 {

                    if($tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',0)->first())
                        {
                             $stargetdata = $tdata->totaltargets;
                     
                     //$targetdata = $tdata->totaltargets;

                             if($paumentdats > $stargetdata)
                             {
                               $tdata->statsus  =  1;
                               $tdata->save();

                             }

                             else
                             {

                             }
                            
                            
                        }
                        
                        
                        else
                        {
                            
                            $tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',1)->first();
                             $stargetdata = $tdata->totaltargets;
                     
                     //$targetdata = $tdata->totaltargets;

                             if($paumentdats > $stargetdata)
                             {
                               $tdata->statsus  =  1;
                               $tdata->save();

                             }

                             else
                             {

                             }
                            
                        }

                       
                 }

         }

         else
         {
            $stargetdata = 0;
         }
                
                
               /*$paumentdats = 146500;*/

        $invodata = admissionprocess::whereMonth('sadate', $currentMonth)->sum('invtotal');
       
        $conversionstatus = leads::where('conversationstatus','1')->count();
      
        $incent = $targetdata - $paumentdats;
      
        $dates = date('Y-m-d');
         $cvruadmissions = admissionprocess::where('suniversities','CVRU(BL)')->whereMonth('sadate', $currentMonth)->get();
        
          $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();

          $regetdatas_ss = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->groupBy('payments.reinviceid')->latest('payments.nexamountdate')->get();
          
             $ipaddress = '';
       if (isset($_SERVER['HTTP_CLIENT_IP']))
           $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
       else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_X_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
       else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
       else if(isset($_SERVER['REMOTE_ADDR']))
           $ipaddress = $_SERVER['REMOTE_ADDR'];
       else
           $ipaddress = 'UNKNOWN';  
           
           
          // dd($ipaddress);
           
           
      
        return view('home',compact('getdatas_ss','regetdatas_ss','targetdata','leadsdatas','conversionstatus','invodata','paumentdats','incent','dates','almarkusrt','totaladmission','branchalldata','cvruadmissions','ipaddress'));
    }


    public function admin()
    {
           $ipaddress = '';
       if (isset($_SERVER['HTTP_CLIENT_IP']))
           $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
       else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_X_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
       else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
       else if(isset($_SERVER['REMOTE_ADDR']))
           $ipaddress = $_SERVER['REMOTE_ADDR'];
       else
           $ipaddress = 'UNKNOWN';
        
        $username = Auth::user()->name;
        $userId = Auth::user()->id;
        $UserBranch = Auth::user()->branchs;

        $startdate = assigntarget::where('tassignuser',$username)->pluck('startsdates');
        $enddate = assigntarget::where('tassignuser',$username)->pluck('enddates');
        $dates = date('Y-m-d');
        $currentMonth = date('m');

        $folss = followup::get();
        $userBranch = Auth::user()->branchs;
        $userdata = User::where('branchs',$userBranch)->get();
        

        $leadsdatas = leads::where('branch',$UserBranch)->whereMonth('leaddate',$currentMonth)->count();

         $totaladmission = admissionprocess::where('stobranches',$UserBranch)->whereMonth('sadate', $currentMonth)->count();

        //dd($UserBranch);
        $usersIDS = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->pluck('id');
       
          $tshid = assigntarget::where('tbranch',$UserBranch)->whereMonth('enddates',$currentMonth)->pluck('id');
        //  dd($tshid);
          $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$UserBranch)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',0)->first();

         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$UserBranch)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
        
            if($tdata)
            {
                $targetdata = $tdata->totaltargets;
            }
            elseif($ntdadat)
            {
               $targetdata = $ntdadat->totaltargets;
            }

            else
            {
                $targetdata = 0;
            }


             $refuncalculcation = DB::table('refunds')->where('refunds.rfromsbranchs',$UserBranch)->whereMonth('recollectionsmonths',$currentMonth)->get()->sum('resettlementsamounts');
                                                       $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$UserBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

                                                            $ss_sum = 0;
                                                            $ins_sum = 0;
                                                            $cvru_sum = 0;
                                                             $sap_sum = 0;
                                                            foreach($namesfinds as $students)
                                                            {
                                                                     $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $getcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $cvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                       $cvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();

                                                                        $sapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $getsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                   if($getcvrusrefunds)
                                                                   {


                                                                    if($cvrufeessdetaiks)
                                                                    {

                                                                        $cvru_sum  +=  $cvrusfesscalcultations->cvrufees;
                                                                    }



                                                                           
                                                                            
                                                                

                                                                  }

                                                                   if($getsapfeesrefunds)
                                                                    {

                                                                         if($sapfeesdetails)
                                                                        {

                                                                        $sap_sum  +=  $sapfeesdetails->sapfees;
                                                                                                                                           
                                                                        }

                                                                    }

                                                                  if($getrefunds)
                                                                    {
                                                                         if($getrefunds->refunenrollmentsno != null)
                                                                            {

                                                                            }


                                                                    }
                                                                    else
                                                                    {
                                                                        if($students->studenterno != null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }

                                                                        else if($students->studenterno == null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                             else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            }


                                                            $freshadmissions = $ss_sum + $ins_sum;


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$UserBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

                                                            $rins_sum  = 0;
                                                            $rcvru_sum = 0;
                                                            $rsap_sum = 0;
                                                            foreach($reinvoicesdata as $students)
                                                            {
                                                                     $regetrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $regetcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $recvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                        $recvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();


                                                                         $resapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $regetsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                   if($regetcvrusrefunds)
                                                                   {


                                                                    if($recvrufeessdetaiks)
                                                                    {

                                                                        $rcvru_sum  +=  $recvrusfesscalcultations->cvrufees;
                                                                    }



                                                                           
                                                                            
                                                                

                                                                  }

                                                                    if($regetsapfeesrefunds)
                                                                    {

                                                                         if($resapfeesdetails)
                                                                        {

                                                                        $rsap_sum  +=  $sapfeesdetails->sapfees;
                                                                                                                                           
                                                                        }

                                                                    }


                                                                  if($getrefunds)
                                                                    {
                                                                         if($getrefunds->refunenrollmentsno != null)
                                                                            {

                                                                            }


                                                                    }
                                                                    else
                                                                    {
                                                                        

                                                                        if($students->reinviceid)
                                                                        {
                                                                            if($recvrufeessdetaiks)
                                                                            {
                                                                                if($recvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $rins_sum  += abs($recvrufeessdetaiks->cvrufees - $students->paymentreceived - $students->rgstprices);
                                                                                }
                                                                                else if($recvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                                }
                                                                            }

                                                                            else if($resapfeesdetails)
                                                                            {

                                                                                 if($resapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $rins_sum  += abs($resapfeesdetails->sapfees - $students->paymentreceived - $students->rgstprices);
                                                                                }
                                                                                else if($resapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                                }

                                                                            }

                                                                            else
                                                                            {
                                                                                 $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            }


                                                  
                                     
                               $totalpaymentreceives = $rins_sum + $freshadmissions;

                                $ovalcal = $refuncalculcation;
                               
                                
                                             $paumentdats = $totalpaymentreceives;             
                                    



                
        $stshid = assigntarget::where('tbranch','BITSJ')->whereMonth('enddates',$currentMonth)->pluck('id');
          if($targetdata < $paumentdats)
         {
                 foreach($stshid as $tid)
                 {

                    if($tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',0)->first())
                        {
                             $stargetdata = $tdata->totaltargets;
                     
                     //$targetdata = $tdata->totaltargets;

                             if($paumentdats > $stargetdata)
                             {
                               $tdata->statsus  =  1;
                               $tdata->save();

                             }

                             else
                             {

                             }
                            
                            
                        }
                        
                        
                        else
                        {
                            
                            $tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',1)->first();
                             $stargetdata = $tdata->totaltargets;
                     
                     //$targetdata = $tdata->totaltargets;

                             if($paumentdats > $stargetdata)
                             {
                               $tdata->statsus  =  1;
                               $tdata->save();

                             }

                             else
                             {

                             }
                            
                        }
                    

                       
                 }

         }

         else
         {
            $stargetdata = 0;
         }

        $invodata = admissionprocess::whereMonth('sadate', $currentMonth)->sum('invtotal');
       
        $conversionstatus = leads::where('conversationstatus','1')->count();
      
        $incent = $targetdata - $paumentdats;
      
        $dates = date('Y-m-d');
      
        $marketlead = leads::where('user_id',$userId)->get();
        
        $almarkusrt = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->get(); 
          
          
          $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();

          $regetdatas_ss = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->groupBy('payments.reinviceid')->latest('payments.nexamountdate')->get();
          
        return view('neasminshome',compact('getdatas_ss','regetdatas_ss','targetdata','leadsdatas','conversionstatus','invodata','paumentdats','incent','marketlead','dates','almarkusrt','folss','userdata','totaladmission','ipaddress'));
    }
       
        
        

     public function marketuse(assigntarget $assigntarget,leads $leads,payment $payment)
    {
       
          $ipaddress = '';
                   if (isset($_SERVER['HTTP_CLIENT_IP']))
                       $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
                   else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                       $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                   else if(isset($_SERVER['HTTP_X_FORWARDED']))
                       $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
                   else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
                       $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
                   else if(isset($_SERVER['HTTP_FORWARDED']))
                       $ipaddress = $_SERVER['HTTP_FORWARDED'];
                   else if(isset($_SERVER['REMOTE_ADDR']))
                       $ipaddress = $_SERVER['REMOTE_ADDR'];
                   else
                       $ipaddress = 'UNKNOWN';  
           
                $username = Auth::user()->name;
                $userId = Auth::user()->id;
                $UserBranch = Auth::user()->branchs;

                $startdate = assigntarget::where('tassignuser',$username)->pluck('startsdates');
                $enddate = assigntarget::where('tassignuser',$username)->pluck('enddates');
                $dates = date('Y-m-d');
                $currentMonth = date('m');

                $folss = followup::get();
                $userBranch = Auth::user()->branchs;
                $userdata = User::where('branchs',$userBranch)->get();
                

                $leadsdatas = leads::where('user_id',$userId)->whereMonth('leaddate',$currentMonth)->count();

                 /*$totaladmission = admissionprocess::where('admissionsusersid',$userId)->whereMonth('sadate', $currentMonth)->count();*/
                 
                 $totaladmission = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.admissionsusersid',$userId)->where('payments.studenterno','!=',NULl)->whereMonth('sadate', $currentMonth)->count();
                 
                 $pid = admissionprocess::where('admissionsusersid',$userId)->pluck('id');


                 $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$username)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',0)->first();

                 $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$username)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
                
                    if($tdata)
                    {
                        $targetdata = $tdata->totaltargets;
                    }
                    elseif($ntdadat)
                    {
                       $targetdata = $ntdadat->totaltargets;
                    }

                    else
                    {
                        $targetdata = 0;
                    }
       

                  $refuncalculcation = DB::table('refunds')->where('refunds.rformsusers',$userId)->whereMonth('recollectionsmonths',$currentMonth)->get()->sum('resettlementsamounts');
                   $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$userId)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

                                                            $ss_sum = 0;
                                                            $ins_sum = 0;
                                                            $cvru_sum = 0;
                                                            foreach($namesfinds as $students)
                                                            {
                                                                     $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $getcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $cvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                       $cvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();

                                                                         $resapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $regetsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                             $sapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $getsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                   if($getcvrusrefunds)
                                                                   {


                                                                    if($cvrufeessdetaiks)
                                                                    {

                                                                        $cvru_sum  +=  $cvrusfesscalcultations->cvrufees;
                                                                    }



                                                                           
                                                                            
                                                                

                                                                  }

                                                                   if($getsapfeesrefunds)
                                                                    {

                                                                         if($sapfeesdetails)
                                                                        {

                                                                        $sap_sum  +=  $sapfeesdetails->sapfees;
                                                                                                                                           
                                                                        }

                                                                    }

                                                                  if($getrefunds)
                                                                    {
                                                                         if($getrefunds->refunenrollmentsno != null)
                                                                            {

                                                                            }


                                                                    }
                                                                    else
                                                                    {
                                                                        if($students->studenterno != null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }

                                                                        else if($students->studenterno == null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                             else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            


                                                                    
                                                            }


                                                            $freshadmissions = $ss_sum + $ins_sum;


                 
                                     
                               $totalpaymentreceives =  $freshadmissions;

                               $ovalcal = $refuncalculcation;
                               
                                
                                             $paumentdats = $totalpaymentreceives;
                  

                $invodata = admissionprocess::where('admissionsusersid',$userId)->whereMonth('sadate', $currentMonth)->sum('invtotal');
               
                $conversionstatus = admissionprocess::where('admissionsusersid',$userId)->whereMonth('sadate', $currentMonth)->count();
                
                
              
                $incent = $targetdata - $paumentdats;
              
                $dates = date('Y-m-d');
              
               $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.admissionsusersid',$userId)->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();


                return view('markethome',compact('getdatas_ss','targetdata','leadsdatas','conversionstatus','invodata','paumentdats','incent','dates','folss','userdata','totaladmission','ipaddress'));
    }

    public function centermanagerdashboard(assigntarget $assigntarget,leads $leads,payment $payment)
    {
          $ipaddress = '';
       if (isset($_SERVER['HTTP_CLIENT_IP']))
           $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
       else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_X_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
       else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
           $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
       else if(isset($_SERVER['HTTP_FORWARDED']))
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
       else if(isset($_SERVER['REMOTE_ADDR']))
           $ipaddress = $_SERVER['REMOTE_ADDR'];
       else
           $ipaddress = 'UNKNOWN';  
        
        
        $username = Auth::user()->name;
        $userId = Auth::user()->id;
        $UserBranch = Auth::user()->branchs;
         $branchalldata = Branch::where('branchname',$UserBranch)->get();

        $startdate = assigntarget::where('tassignuser',$username)->pluck('startsdates');
        $enddate = assigntarget::where('tassignuser',$username)->pluck('enddates');
        $dates = date('Y-m-d');
        $currentMonth = date('m');

        $folss = followup::get();
        $userBranch = Auth::user()->branchs;
        $userdata = User::where('branchs',$userBranch)->get();
        

        $leadsdatas = leads::where('branch',$UserBranch)->whereMonth('leaddate',$currentMonth)->count();

         $totaladmission = admissionprocess::where('stobranches',$UserBranch)->whereMonth('sadate', $currentMonth)->count();

        //dd($UserBranch);
        $usersIDS = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->pluck('id');
       
          $tshid = assigntarget::where('tbranch',$UserBranch)->whereMonth('enddates',$currentMonth)->pluck('id');
             $refuncalculcation = DB::table('refunds')->where('refunds.rfromsbranchs',$UserBranch)->whereMonth('recollectionsmonths',$currentMonth)->get()->sum('resettlementsamounts');
             
       $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$UserBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

                                                            $ss_sum = 0;
                                                            $ins_sum = 0;
                                                            $cvru_sum = 0;
                                                             $sap_sum = 0;
                                                            foreach($namesfinds as $students)
                                                            {
                                                                     $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $getcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $cvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                       $cvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();

                                                                        $sapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $getsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                   if($getcvrusrefunds)
                                                                   {


                                                                    if($cvrufeessdetaiks)
                                                                    {

                                                                        $cvru_sum  +=  $cvrusfesscalcultations->cvrufees;
                                                                    }



                                                                           
                                                                            
                                                                

                                                                  }

                                                                   if($getsapfeesrefunds)
                                                                    {

                                                                         if($sapfeesdetails)
                                                                        {

                                                                        $sap_sum  +=  $sapfeesdetails->sapfees;
                                                                                                                                           
                                                                        }

                                                                    }

                                                                  if($getrefunds)
                                                                    {
                                                                         if($getrefunds->refunenrollmentsno != null)
                                                                            {

                                                                            }


                                                                    }
                                                                    else
                                                                    {
                                                                        if($students->studenterno != null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ss_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ss_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }

                                                                        else if($students->studenterno == null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($cvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                             else if($sapfeesdetails)
                                                                            {
                                                                                if($sapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($sapfeesdetails->sapfees - $students->paymentreceived  - $students->gstprices);
                                                                                }
                                                                                else if($sapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ins_sum  += $students->paymentreceived  - $students->gstprices;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            }


                                                            $freshadmissions = $ss_sum + $ins_sum;


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$UserBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

                                                            $rins_sum  = 0;
                                                            $rcvru_sum = 0;
                                                            $rsap_sum = 0;
                                                            foreach($reinvoicesdata as $students)
                                                            {
                                                                     $regetrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $regetcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $recvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                        $recvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();


                                                                         $resapfeesdetails = sapaccounting::where('sapid',$students->pids)->first();

                                                                     $regetsapfeesrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('sapaccountings','sapaccountings.sapid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                   if($regetcvrusrefunds)
                                                                   {


                                                                    if($recvrufeessdetaiks)
                                                                    {

                                                                        $rcvru_sum  +=  $recvrusfesscalcultations->cvrufees;
                                                                    }



                                                                           
                                                                            
                                                                

                                                                  }

                                                                    if($regetsapfeesrefunds)
                                                                    {

                                                                         if($resapfeesdetails)
                                                                        {

                                                                        $rsap_sum  +=  $sapfeesdetails->sapfees;
                                                                                                                                           
                                                                        }

                                                                    }


                                                                  if($getrefunds)
                                                                    {
                                                                         if($getrefunds->refunenrollmentsno != null)
                                                                            {

                                                                            }


                                                                    }
                                                                    else
                                                                    {
                                                                        

                                                                        if($students->reinviceid)
                                                                        {
                                                                            if($recvrufeessdetaiks)
                                                                            {
                                                                                if($recvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $rins_sum  += abs($recvrufeessdetaiks->cvrufees - $students->paymentreceived - $students->rgstprices);
                                                                                }
                                                                                else if($recvrufeessdetaiks->cvrufees == 0)
                                                                                {
                                                                                    $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                                }
                                                                            }

                                                                            else if($resapfeesdetails)
                                                                            {

                                                                                 if($resapfeesdetails->sapfees != 0)
                                                                                {
                                                                                    $rins_sum  += abs($resapfeesdetails->sapfees - $students->paymentreceived - $students->rgstprices);
                                                                                }
                                                                                else if($resapfeesdetails->sapfees == 0)
                                                                                {
                                                                                    $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                                }

                                                                            }

                                                                            else
                                                                            {
                                                                                 $rins_sum  += $students->paymentreceived - $students->rgstprices;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            }


                                                  
                                     
                               $totalpaymentreceives = $rins_sum + $freshadmissions;

                                $ovalcal = $refuncalculcation;
                               
                                
                                             $paumentdats = $totalpaymentreceives;        

           
        
          $stshid = assigntarget::where('tbranch','BITSJ')->whereMonth('enddates',$currentMonth)->pluck('id');
          /*if(!$stshid->isEmpty())
         {
                 foreach($stshid as $tid)
                 {

                    $tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',0)->first();

                     $stargetdata = $tdata->totaltargets;
                     
                     //$targetdata = $tdata->totaltargets;

                             if($paumentdats > $stargetdata)
                             {
                               $tdata->statsus  =  1;
                               $tdata->save();

                             }

                       
                 }

         }

         else
         {
            $stargetdata = 0;
         }
          */   
          $tshid = assigntarget::where('tbranch',$UserBranch)->whereMonth('enddates',$currentMonth)->pluck('id');
        //  dd($tshid);
          $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$UserBranch)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',0)->first();

         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$UserBranch)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
        
            if($tdata)
            {
                $targetdata = $tdata->totaltargets;
            }
            elseif($ntdadat)
            {
               $targetdata = $ntdadat->totaltargets;
            }

            else
            {
                $targetdata = 0;
            }

        $invodata = admissionprocess::whereMonth('sadate', $currentMonth)->sum('invtotal');
       
        $conversionstatus = leads::where('conversationstatus','1')->count();
      
        $incent = $targetdata - $paumentdats;
      
        $dates = date('Y-m-d');
      
        $marketlead = leads::where('user_id',$userId)->get();
        
        $almarkusrt = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->get();
 

       $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();

          $regetdatas_ss = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->groupBy('payments.reinviceid')->latest('payments.nexamountdate')->get();
      

        return view('centermanagerhome',compact('getdatas_ss','regetdatas_ss','branchalldata','targetdata','leadsdatas','conversionstatus','invodata','paumentdats','incent','marketlead','dates','almarkusrt','folss','userdata','totaladmission','ipaddress'));
    }

     public function brnch(assigntarget $assigntarget,leads $leads,payment $payment)
    {
        $username = Auth::user()->name;
        $userId = Auth::user()->id;
        $UserBranch = Auth::user()->branchs;

        //dd($UserBranch);
        $usersIDS = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->pluck('id');
        //dd($usersIDS);
        $targetdata = assigntarget::where('tbranch',$UserBranch)->sum('targetamount');
        //dd($targetdata);
        $paumentdats = payment::where('branchs',$UserBranch)->sum('paymentreceived');

        $leadsdatas = leads::where('branch',$UserBranch)->count();
            $invodata = admissionprocess::where('sbrnanch',$UserBranch)->count();
        $conversionstatus = leads::where('branch',$UserBranch)->where('conversationstatus','1')->count();
       // dd($paumentdats);
        $incent = $targetdata - $paumentdats;
        //dd($incent);
        $dates = date('Y-m-d');
       //dd($incent);
          $marketlead = leads::where('user_id',$userId)->get();
        
        $almarkusrt = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->get();
    //    dd($almarkusrt);

        return view('branchhome',compact('targetdata','leadsdatas','conversionstatus','invodata','paumentdats','incent','marketlead','dates','almarkusrt'));
    }


    public function centercordinator(assigntarget $assigntarget,leads $leads,payment $payment)
    {
        $username = Auth::user()->name;
        $userId = Auth::user()->id;
        $UserBranch = Auth::user()->branchs;

        //dd($UserBranch);
        $usersIDS = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->pluck('id');
        //dd($usersIDS);
        $targetdata = assigntarget::where('tbranch',$UserBranch)->sum('targetamount');
        //dd($targetdata);
        $paumentdats = payment::where('branchs',$UserBranch)->sum('paymentreceived');

        $leadsdatas = leads::where('branch',$UserBranch)->count();
            $invodata = admissionprocess::where('sbrnanch',$UserBranch)->count();
        $conversionstatus = leads::where('branch',$UserBranch)->where('conversationstatus','1')->count();
       // dd($paumentdats);
        $incent = $targetdata - $paumentdats;
        //dd($incent);
        $dates = date('Y-m-d');
       //dd($incent);
          $marketlead = leads::where('user_id',$userId)->get();
        
        $almarkusrt = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->get();
    //    dd($almarkusrt);

        return view('centercordinator',compact('targetdata','leadsdatas','conversionstatus','invodata','paumentdats','incent','marketlead','dates','almarkusrt'));
    }


    public function afmarketuser()
    {
       
         $username = Auth::user()->name;
        $userId = Auth::user()->id;
        $UserBranch = Auth::user()->branchs;
        $UserAcategory = Auth::user()->uafficategory;

        $startdate = assigntarget::where('tassignuser',$username)->pluck('startsdates');
        $enddate = assigntarget::where('tassignuser',$username)->pluck('enddates');
        $dates = date('Y-m-d');
        $currentMonth = date('m');

        $folss = followup::get();
        $userBranch = Auth::user()->branchs;
        $userdata = User::where('branchs',$userBranch)->get();
         $termsconditionsdetails = termsandconditions::where('rulecategories',$UserAcategory)->get();

        $leadsdatas = leads::where('user_id',$userId)->whereMonth('leaddate',$currentMonth)->count();

         $totaladmission = admissionprocess::where('admissionsusersid',$userId)->whereMonth('sadate', $currentMonth)->count();
         $pid = admissionprocess::where('admissionsusersid',$userId)->pluck('id');
       
         $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$UserBranch)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',0)->first();

         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$UserBranch)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
        
            if($tdata)
            {
                $targetdata = $tdata->totaltargets;
            }
            elseif($ntdadat)
            {
               $targetdata = $ntdadat->totaltargets;
            }

            else
            {
                $targetdata = 0;
            }

        
         $paumentdats = payment::where('branchs',$UserBranch)->whereMonth('paymentdate', $currentMonth)->sum('paymentreceived');
        $invodata = admissionprocess::where('admissionsusersid',$userId)->whereMonth('sadate', $currentMonth)->sum('invtotal');
       
        $conversionstatus = admissionprocess::where('admissionsusersid',$userId)->whereMonth('sadate', $currentMonth)->count();
      
        $incent = $targetdata - $paumentdats;
      
        $dates = date('Y-m-d');
      
      //  $marketlead = leads::where('user_id',$userId)->get();
        
     //   $almarkusrt = User::where('usercategory','Marketing')->where('branchs',$UserBranch)->get();
 

       
       /* if($incent == 0.0)
        {
               if($updatesx  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',1)->first())
                {
                       $targetdata = TargetAlloted::where('targetuserid',$tshid)->where('statsus',1)->sum('totaltargets');
                }   

                else
                {
                    $updatesx  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
                    $updatesx->statsus = 1;
                    $updatesx->save();
                }
        }
        
        else
        {
             $updatesx  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
                $updatesx->statsus = 0;
                $updatesx->save();
        }*/


        return view('affiliatemarketinguserhome',compact('targetdata','leadsdatas','conversionstatus','invodata','paumentdats','incent','dates','folss','userdata','totaladmission','termsconditionsdetails'));
    }


    public function facultydashboard()
    {

        return view('facultyhome');
    }


    public function ajaxdata($homebranches)
    {
        if($homebranches == 'BIT Baroda Sayajigunj')
        {
            $sum = payment::where('branchs','=',$homebranches)->sum('paymentreceived');
            $ds = leads::where('branch','=',$homebranches)->count();

            return response()->json($sum);
        }

        else if($homebranches == 'BIT Baroda Manjalpur')
        {
            $sum = payment::where('branchs','=',$homebranches)->sum('paymentreceived');

            return response()->json($sum);
        }

        else if($homebranches == 'BIT Baroda Waghodia')
        {
            $sum = payment::where('branchs','=',$homebranches)->sum('paymentreceived');

            return response()->json($sum);
        }
    }

    public function leaddata($newhomebranches)
    {
         if($newhomebranches == 'BIT Baroda Sayajigunj')
        {
            $ds = leads::where('branch','=',$newhomebranches)->count();

            return response()->json($ds);
        }

        else if($newhomebranches == 'BIT Baroda Manjalpur')
        {
            $ds = leads::where('branch','=',$newhomebranches)->count();

            return response()->json($ds);
        }

        else if($newhomebranches == 'BIT Baroda Waghodia')
        {
           $ds = leads::where('branch','=',$newhomebranches)->count();

            return response()->json($ds);
        }
    }
    public function totaladmission($branchtotaladmission)
    {
         if($branchtotaladmission == 'BIT Baroda Sayajigunj')
        {
            $ap = admissionprocess::where('sbrnanch','=',$branchtotaladmission)->count();

            return response()->json($ap);
        }

        else if($branchtotaladmission == 'BIT Baroda Manjalpur')
        {
            $ap = admissionprocess::where('sbrnanch','=',$branchtotaladmission)->count();

            return response()->json($ap);
        }

        else if($branchtotaladmission == 'BIT Baroda Waghodia')
        {
           $ap = admissionprocess::where('sbrnanch','=',$branchtotaladmission)->count();

            return response()->json($ap);
        }
    }

    public function usertotalcalculation($marketnguserid)
    {


      /*  $urensme = User::where('id',$marketnguserid)->pluck('name');
         $targetdata = assigntarget::where('tassignuser',$urensme)->sum('targetamount');*/
            $pid = admissionprocess::where('admissionsusersid',$marketnguserid)->pluck('id');

            $paumentdats = payment::where('inviceid',$pid)->sum('paymentreceived');
             /* $paumentdats = payment::where('userid',$marketnguserid)->get();*/


         return response()->json($paumentdats); 
    }

    public function toatlatarger($marketingtaptaltarget)
    {

        /*$urensme = User::where('id',$marketingtaptaltarget)->pluck('name');
         $targetdata = assigntarget::where('tassignuser',$urensme)->sum('targetamount');*/
          $tshid = assigntarget::where('tassignuser',$marketingtaptaltarget)->pluck('id');
        $targetdata = TargetAlloted::where('targetuserid',$tshid)->sum('targetamounts');

         return response()->json($targetdata); 
    }


    public function totallead($markusri)
    {

        //$urensme = User::where('id',$marketnguserid)->pluck('name');

        $currentMonth = date('m');
        
         $leadsdatas = leads::where('user_id',$markusri)->whereMonth('leaddate',$currentMonth)->count();

         return response()->json($leadsdatas); 
    }


    public function toaladmission($markeusadmi)
    {

        //$urensme = User::where('id',$marketnguserid)->pluck('name');
          $currentMonth = date('m');

             $conversionstatus = admissionprocess::where('admissionsusersid',$markeusadmi)->whereMonth('sadate', $currentMonth)->count();

         return response()->json($conversionstatus); 
    }


     public function totalinvoices($marketnguserid)
    {

        //$urensme = User::where('id',$marketnguserid)->pluck('name');
          $currentMonth = date('m');

             $conversionstatus = admissionprocess::where('admissionsusersid',$marketnguserid)->whereMonth('sadate', $currentMonth)->sum('invtotal');

         return response()->json($conversionstatus); 
    }

    
    public function targetcal($branchtargert)
    {
             if($branchtargert == 'BIT Baroda Sayajigunj')
        {
            

             $targetdata = assigntarget::where('tbranch','=',$branchtargert)->sum('targetamount');

            return response()->json($targetdata);
        }

        else if($branchtargert == 'BIT Baroda Manjalpur')
        {
            $targetdata = assigntarget::where('tbranch','=',$branchtargert)->sum('targetamount');


            return response()->json($targetdata);
        }

        else if($branchtargert == 'BIT Baroda Waghodia')
        {
           $targetdata = assigntarget::where('tbranch','=',$branchtargert)->sum('targetamount');

            return response()->json($targetdata);
        }

    }

    public function targetpending($branchwisetargetpending)
    {
        if($branchwisetargetpending == 'BIT Baroda Sayajigunj')
        {
            
            $sum = payment::where('branchs','=',$branchwisetargetpending)->sum('paymentreceived');
             $targetdata = assigntarget::where('tbranch','=',$branchwisetargetpending)->sum('targetamount');

             $pending = $targetdata - $sum;


            return response()->json($pending);
        }

        else if($branchwisetargetpending == 'BIT Baroda Manjalpur')
        {
             $sum = payment::where('branchs','=',$branchwisetargetpending)->sum('paymentreceived');
             $targetdata = assigntarget::where('tbranch','=',$branchwisetargetpending)->sum('targetamount');

             $pending = $targetdata - $sum;
            return response()->json($pending);
        }

        else if($branchwisetargetpending == 'BIT Baroda Waghodia')
        {
            $sum = payment::where('branchs','=',$branchwisetargetpending)->sum('paymentreceived');
             $targetdata = assigntarget::where('tbranch','=',$branchwisetargetpending)->sum('targetamount');

             $pending = $targetdata - $sum;


            return response()->json($pending);
        }
    }


    public function targetpenfing($tagerpendinf)
    {

         $tshid = assigntarget::where('tassignuser',$marketingtaptaltarget)->pluck('id');
        $targetdata = TargetAlloted::where('targetuserid',$tshid)->sum('targetamounts');

          $pid = admissionprocess::where('admissionsusersid',$userId)->pluck('id');

            $paumentdats = payment::where('inviceid',$pid)->sum('paymentreceived');

        /*$userteat = User::where('id',$tagerpendinf)->pluck('name');
         $sum = payment::where('userid','=',$tagerpendinf)->sum('paymentreceived');
             $targetdata = assigntarget::where('tassignuser','=',$userteat)->sum('targetamount');*/

             $pending = $targetdata - $sum;


            return response()->json($pending);
    }

    public function targetachieveds($achievement)
    {
            $userteat = User::where('id',$achievement)->pluck('name');



         $tshid = assigntarget::where('tassignuser',$achievement)->pluck('id');
        $targetdata = TargetAlloted::where('targetuserid',$tshid)->sum('targetamounts');

          $pid = admissionprocess::where('admissionsusersid',$achievement)->pluck('id');

            $paumentdats = payment::where('inviceid',$pid)->sum('paymentreceived');
          

             $pending = $targetdata - $sum;


             if($pending == '0')
             {
                $msg = $userteat.' Achieved Target';
             
                $print = "<h5 class='mb-1 mt-1 text-green blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                 return response()->json($print);
                    
             }

             else
             {
                $msg = $userteat.' not Achieved Target';
                $print = "<h5 class='mb-1 mt-1 text-red blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                return response()->json($print);
             }
    }


    public function achieve($brachwiseachieve)
    {
          if($brachwiseachieve == 'BIT Baroda Sayajigunj')
        {
            
            $sum = payment::where('branchs','=',$brachwiseachieve)->sum('paymentreceived');
             $targetdata = assigntarget::where('tbranch','=',$brachwiseachieve)->sum('targetamount');

             $pending = $targetdata - $sum;

             if($pending == '0')
             {
                $msg = $brachwiseachieve.' Achieved Target';
                $print = "<h5 class='mb-1 mt-1 text-green blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                 return response()->json($print);
             }

             else
             {
                $msg = $brachwiseachieve.' not Achieved Target';
                $print = "<h5 class='mb-1 mt-1 text-red blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                return response()->json($print);
             }
            
        }

        

        else if($brachwiseachieve == 'BIT Baroda Manjalpur')
        {
             $sum = payment::where('branchs','=',$brachwiseachieve)->sum('paymentreceived');
             $targetdata = assigntarget::where('tbranch','=',$brachwiseachieve)->sum('targetamount');

             $pending = $targetdata - $sum;
           if($pending == '0')
             {
                $msg = $brachwiseachieve.' Achieved Target';
                $print = "<h5 class='mb-1 mt-1 text-green blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                 return response()->json($print);
             }

             else
             {
                $msg = $brachwiseachieve.' not Achieved Target';
                $print = "<h5 class='mb-1 mt-1 text-red blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                return response()->json($print);
             }
        }

        else if($brachwiseachieve == 'BIT Baroda Waghodia')
        {
            $sum = payment::where('branchs','=',$brachwiseachieve)->sum('paymentreceived');
             $targetdata = assigntarget::where('tbranch','=',$brachwiseachieve)->sum('targetamount');

             $pending = $targetdata - $sum;
            if($pending == '0')
             {
                $msg = $brachwiseachieve.' Achieved Target';
                $print = "<h5 class='mb-1 mt-1 text-green blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                 return response()->json($print);
             }

             else
             {
                $msg = $brachwiseachieve.' not Achieved Target';
                $print = "<h5 class='mb-1 mt-1 text-red blink-hard'>".$msg."</h5><p class='text-muted mb-0'>Achieve Target</p>";
                return response()->json($print);
             }
        }
    }

    public function getleadstables(Request $request)
        {
             $luserid = $request->leaduserid;
             $data= array();
             $result = leads::where('user_id',$luserid)->get();
             //$result = leads::where('leadsfrom','=',$le)->get();
        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->studentname;
            $row[] = $res->course;
            $row[] = $res->phone;
            $row[] = $res->whatsappno;
            $row[] = $res->email;
            $row[] = $row[] = '<center><button type="button" class="btn btn-primary waves-effect waves-light" onclick="followupfunction('.$res->id.')"><i class="fa fa-tty"></i></button></center>';
            $data[] = $row;

            
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
        }
}
