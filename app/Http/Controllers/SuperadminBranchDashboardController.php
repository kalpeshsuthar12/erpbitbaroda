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
use App\User;
use App\Refund;
use App\CvruFees;
use App\sapaccounting;
use Auth;
use DB;
use Illuminate\Http\Request;

class SuperadminBranchDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
       

        $fby = $request->FilterBy;
       // dd($fby);

       if($getUser = $request->markertuswer)
        {
            
            $getBranch ="";
              $almarkusrt = User::where('usercategory','Marketing')->get();
                    $branchalldata = Branch::all();
                    $getDates = $request->startDates; 
                    
                     $getUserName = User::where('id',$getUser)->pluck('name');


                    $newDats = explode("-",$getDates);

                   // dd($newDats);
                    $leadsdatas = leads::where('user_id',$getUser)->whereYear('leaddate', $newDats[0])->whereMonth('leaddate', $newDats[1])->count();
                   // $conversionstatus = admissionprocess::where('admissionsusersid',$getUser)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->count();
                    
                    
                    $conversionstatus = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.admissionsusersid',$getUser)->where('payments.studenterno','!=',NULl)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->count();
                    
                    $invodata = admissionprocess::where('admissionsusersid',$getUser)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->sum('invtotal');
                     /*$tshid = assigntarget::where('tassignuser',$getUserName)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
                        
                      //  dd($tshid);
                       

                        $tdata  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
                           if ($tdata) 
                           {

                                    $targetdata = $tdata->totaltargets;
                           }

                           else
                           {
                                $targetdata = 0;
                           }*/

                    $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$getUserName)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->where('target_alloteds.statsus',0)->orderby('target_alloteds.id','DESC')->first();

                         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$getUserName)->whereYear('assigntargets.enddates', $newDats[0])->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
                        
                            if($tdata)
                            {
                                $targetdata = $tdata->totaltargets;
                              //  dd($targetdata);
                            }
                            elseif($ntdadat)
                            {
                               $targetdata = $ntdadat->totaltargets;
                            }

                            else
                            {
                                $targetdata = 0;
                            }


                            $invoicesdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->whereYear('payments.paymentdate',$newDats[0])->whereMonth('payments.paymentdate',$newDats[1])->where('admissionprocesses.admissionsusersid',$getUser)->get();
                         //dd($invoicesdata);

                                $refuncalculcation = payment::join('refunds','refunds.refuadmissionid','=','payments.inviceid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->whereMonth('refunds.refunrefundates',$newDats[1])->whereYear('refunds.refunrefundates',$newDats[0])->where('admissionprocesses.admissionsusersid',$getUser)->sum('refunds.refunrefundamounts');
                                
                                $invoicesdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->whereYear('payments.paymentdate',$newDats[0])->whereMonth('payments.paymentdate',$newDats[1])->where('admissionprocesses.admissionsusersid',$getUser)->get();
                         //dd($invoicesdata);

                                /*$refuncalculcation = payment::join('refunds','refunds.refuadmissionid','=','payments.inviceid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->whereMonth('refunds.refunrefundates',$newDats[1])->whereYear('refunds.refunrefundates',$newDats[0])->where('admissionprocesses.admissionsusersid',$getUser)->sum('refunds.refunrefundamounts');*/

                                $refuncalculcation = DB::table('refunds')->where('refunds.rformsusers',$getUser)->whereMonth('recollectionsmonths',$newDats[1])->whereYear('recollectionsmonths',$newDats[0])->get()->sum('resettlementsamounts');

                                
                                $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$getUser)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();

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

 

                             

        $stshid = assigntarget::where('tassignuser',$getUserName)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
        //dd($stshid);

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
                      
                      
                      $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.admissionsusersid',$getUser)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();

                      $regetdatas_ss = " ";

                    
                   // dd($paumentdats);
                    $incent = $targetdata - $paumentdats;

                    return view('superadminhome',compact('getUser','leadsdatas','almarkusrt','branchalldata','conversionstatus','invodata','targetdata','paumentdats','incent','getUser','getDates','getBranch','getUserName','getdatas_ss'));
        }

        else if($getBranch = $request->branchData)
        {
                $getUser ="";
           
             $almarkusrt = User::where('usercategory','Marketing')->get();
                    $UserBranch = Auth::user()->branchs;
                    $branchalldata = Branch::get(); 
                    $getDates = $request->startDates; 
                    
                     $getUserName = User::where('id',$getUser)->pluck('name');


                    $newDats = explode("-",$getDates);
                    $leadsdatas = leads::where('tobranchs',$getBranch)->whereYear('leaddate', $newDats[0])->whereMonth('leaddate', $newDats[1])->count();
                   // $conversionstatus = admissionprocess::where('stobranches',$getBranch)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->count();
                    
                    
                    $conversionstatus = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$getBranch)->where('payments.studenterno','!=',NULl)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->count();
                    $invodata = admissionprocess::where('stobranches',$getBranch)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->sum('invtotal');
                     $tshid = assigntarget::where('tbranch',$getBranch)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
                     

                     $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$getBranch)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->where('target_alloteds.statsus',0)->first();

                         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$getBranch)->whereYear('assigntargets.enddates', $newDats[0])->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
                        
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


                             $refuncalculcation = DB::table('refunds')->where('refunds.rfromsbranchs',$getBranch)->whereMonth('recollectionsmonths',$newDats[1])->whereYear('recollectionsmonths',$newDats[0])->get()->sum('resettlementsamounts');
                             
                                                                            $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$getBranch)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();

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

                                                            
                                                            
                                                            //  dd($ss_sum);


                                                            $freshadmissions = $ss_sum;
            

                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$getBranch)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();

                                                            
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
                                     
                               $totalpaymentreceives = $rins_sum + $freshadmissions + $ins_sum;
                                          //  dd($ins_sum);
                                         //   dd($rins_sum);
                                $ovalcal = $refuncalculcation;
                               
                                
                                             $paumentdats = $totalpaymentreceives;
                                
                
                                 
                            
                                                


                        $stshid = assigntarget::where('tbranch',$getBranch
                    )->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');

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
         
       
         

                    //dd($paumentdats);
                    $incent = $targetdata - $paumentdats;
                    
                    
                                        $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$getBranch)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$getBranch)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();
                 
                                                          
                 


                 $admissions = 0;
                                                    if ($namesfinds) {
                                                        $inviceid_arr = [];
                                                        foreach (
                                                            $namesfinds
                                                            as $newnamesfinds
                                                        ) {
                                                            $tis = payment::select(
                                                                "id",
                                                                "remainingamount"
                                                            )
                                                                ->where(
                                                                    "inviceid",
                                                                    $newnamesfinds->inviceid
                                                                )
                                                                ->latest("id")
                                                                ->first();
                                                            $inviceid_arr[] =
                                                                $tis->id;
                                                        }
                                                        //print_r($inviceid_arr);exit;
                                                        $inv_unique = array_unique(
                                                            $inviceid_arr
                                                        );
                                                        $totalsd = payment::select(
                                                            "remainingamount"
                                                        )
                                                            ->whereIn(
                                                                "id",
                                                                $inv_unique
                                                            )
                                                            ->get()
                                                            ->sum(
                                                                "remainingamount"
                                                            );
                                                    }

                                                    $readmissions = 0;

                                                    if ($reinvoicesdata) {
                                                        $reinviceid_arr = [];
                                                        foreach (
                                                            $reinvoicesdata
                                                            as $rnewnamesfinds
                                                        ) {
                                                            $tis = payment::select(
                                                                "id",
                                                                "remainingamount"
                                                            )
                                                                ->where(
                                                                    "reinviceid",
                                                                    $rnewnamesfinds->reinviceid
                                                                )
                                                                ->latest("id")
                                                                ->first();
                                                            $inviceid_arr[] =
                                                                $tis->id;
                                                        }
                                                        //print_r($inviceid_arr);exit;
                                                        $inv_unique = array_unique(
                                                            $reinviceid_arr
                                                        );
                                                        $retotalsd = payment::select(
                                                            "remainingamount"
                                                        )
                                                            ->whereIn(
                                                                "id",
                                                                $inv_unique
                                                            )
                                                            ->get()
                                                            ->sum(
                                                                "remainingamount"
                                                            );
                                                    }
                                                    //dd($admissions);

                                                    //$radmissions = 0;

                                                    //$totalsd = $admissions + $radmissions;

                                                    if ($reinvoicesdata) {
                                                        $readmintoalbal = $retotalsd;
                                                    } else {
                                                        $readmintoalbal = 0;
                                                    }

                                                    $totalbal = $totalsd +  $readmintoalbal;
                                                 
                    
                    return view('superadminhome',compact('getUser','getBranch','leadsdatas','almarkusrt','branchalldata','conversionstatus','invodata','targetdata','paumentdats','incent','getBranch','getDates','getUser','totalbal'));
        }
                  

         /*return response()->json($leadsdatas);*/
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create(Request $request)
     {
       
       $UserBranch = Auth::user()->branchs;

        $fby = $request->FilterBy;
       // dd($fby);

       if($getUser = $request->markertuswer)
        {
            
            $getBranch ="";
              $almarkusrt = User::where('usercategory','Marketing')->get();
                   $branchalldata = Branch::where('branchname',$UserBranch)->get();
                    $getDates = $request->startDates; 
                    
                     $getUserName = User::where('id',$getUser)->pluck('name');


                    $newDats = explode("-",$getDates);

                   // dd($newDats);
                    $leadsdatas = leads::where('user_id',$getUser)->whereYear('leaddate', $newDats[0])->whereMonth('leaddate', $newDats[1])->count();
                    $conversionstatus = admissionprocess::where('admissionsusersid',$getUser)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->count();
                    $invodata = admissionprocess::where('admissionsusersid',$getUser)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->sum('invtotal');
                     
                    $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$getUserName)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->where('target_alloteds.statsus',0)->orderby('target_alloteds.id','DESC')->first();

                         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$getUserName)->whereYear('assigntargets.enddates', $newDats[0])->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
                        
                            if($tdata)
                            {
                                $targetdata = $tdata->totaltargets;
                              //  dd($targetdata);
                            }
                            elseif($ntdadat)
                            {
                               $targetdata = $ntdadat->totaltargets;
                            }

                            else
                            {
                                $targetdata = 0;
                            }


                            $invoicesdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->whereYear('payments.paymentdate',$newDats[0])->whereMonth('payments.paymentdate',$newDats[1])->where('admissionprocesses.admissionsusersid',$getUser)->get();
                         

                                $refuncalculcation = DB::table('refunds')->where('refunds.rformsusers',$getUser)->whereMonth('recollectionsmonths',$newDats[1])->whereYear('recollectionsmonths',$newDats[0])->get()->sum('resettlementsamounts');


                                       $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$getUser)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();

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




        $stshid = assigntarget::where('tassignuser',$getUserName)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
        //dd($stshid);

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
            
                      
                    
                   // dd($paumentdats);
                    $incent = $targetdata - $paumentdats;


                    if ($namesfinds) {
                                                        $inviceid_arr = [];
                                                        foreach (
                                                            $namesfinds
                                                            as $newnamesfinds
                                                        ) {
                                                            $tis = payment::select(
                                                                "id",
                                                                "remainingamount"
                                                            )
                                                                ->where(
                                                                    "inviceid",
                                                                    $newnamesfinds->inviceid
                                                                )
                                                                ->latest("id")
                                                                ->first();
                                                            $inviceid_arr[] =
                                                                $tis->id;
                                                        }
                                                        //print_r($inviceid_arr);exit;
                                                        $inv_unique = array_unique(
                                                            $inviceid_arr
                                                        );
                                                        $totalsd = payment::select(
                                                            "remainingamount"
                                                        )
                                                            ->whereIn(
                                                                "id",
                                                                $inv_unique
                                                            )
                                                            ->get()
                                                            ->sum(
                                                                "remainingamount"
                                                            );
                                                    }

                                                   
                                                    $totalbal = $totalsd;

                    return view('filtercentermanager',compact('getUser','leadsdatas','almarkusrt','branchalldata','conversionstatus','invodata','targetdata','paumentdats','incent','getUser','getDates','getBranch','getUserName','totalbal'));
        }

        else if($getBranch = $request->branchData)
        {
                $getUser ="";
           
             $almarkusrt = User::where('usercategory','Marketing')->get();
                    $UserBranch = Auth::user()->branchs;
                     $branchalldata = Branch::where('branchname',$UserBranch)->get();
                    $getDates = $request->startDates; 
                    
                     $getUserName = User::where('id',$getUser)->pluck('name');


                    $newDats = explode("-",$getDates);
                    $leadsdatas = leads::where('tobranchs',$getBranch)->whereYear('leaddate', $newDats[0])->whereMonth('leaddate', $newDats[1])->count();
                    $conversionstatus = admissionprocess::where('stobranches',$getBranch)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->count();
                    $invodata = admissionprocess::where('stobranches',$getBranch)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->sum('invtotal');
                     $tshid = assigntarget::where('tbranch',$getBranch)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
                     

                     $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$getBranch)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->where('target_alloteds.statsus',0)->first();

                         $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tbranch',$getBranch)->whereYear('assigntargets.enddates', $newDats[0])->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
                        
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


                             $refuncalculcation = DB::table('refunds')->where('refunds.rfromsbranchs',$getBranch)->whereMonth('recollectionsmonths',$newDats[1])->whereYear('recollectionsmonths',$newDats[0])->get()->sum('resettlementsamounts');



                                                $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$getBranch)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();

                                                            $ss_sum = 0;
                                                            $ins_sum = 0;
                                                            $cvru_sum = 0;
                                                            foreach($namesfinds as $students)
                                                            {
                                                                     $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $getcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $cvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                       $cvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();

                                                                   if($getcvrusrefunds)
                                                                   {


                                                                    if($cvrufeessdetaiks)
                                                                    {

                                                                        $cvru_sum  +=  $cvrusfesscalcultations->cvrufees;
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
                                                                                    $ss_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived);
                                                                                }
                                                                                else
                                                                                {
                                                                                    $ss_sum  += $students->paymentreceived;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ss_sum  += $students->paymentreceived;
                                                                            }
                                                                        }

                                                                        else if($students->studenterno == null)
                                                                        {
                                                                            if($cvrufeessdetaiks)
                                                                            {
                                                                                if($cvrufeessdetaiks->cvrufees != 0)
                                                                                {
                                                                                    $ins_sum  += abs($cvrusfesscalcultations->cvrufees - $students->paymentreceived);
                                                                                }
                                                                                else
                                                                                {
                                                                                    $ins_sum  += $students->paymentreceived;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $ins_sum  += $students->paymentreceived;
                                                                            }
                                                                        }
                                                                    }

                                                                    
                                                            }


                                                            $freshadmissions = $ss_sum + $ins_sum;


                                                            $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$getBranch)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();

                                                            $rins_sum  = 0;
                                                            $rcvru_sum = 0;
                                                            foreach($reinvoicesdata as $students)
                                                            {
                                                                     $regetrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $regetcvrusrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->join('cvru_fees','cvru_fees.studentid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                      $recvrufeessdetaiks = DB::table("cvru_fees")->where("studentid",$students->pids)->first();
                                                                        $recvrusfesscalcultations = DB::table("cvru_fees")->where("studentid",$students->pids)->first();

                                                                   if($regetcvrusrefunds)
                                                                   {


                                                                    if($recvrufeessdetaiks)
                                                                    {

                                                                        $rcvru_sum  +=  $recvrusfesscalcultations->cvrufees;
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
                                                                                    $rins_sum  += abs($recvrufeessdetaiks->cvrufees - $students->paymentreceived);
                                                                                }
                                                                                else
                                                                                {
                                                                                    $rins_sum  += $students->paymentreceived;
                                                                                }
                                                                            }

                                                                            else
                                                                            {
                                                                                 $rins_sum  += $students->paymentreceived;
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

                
                                

                        $stshid = assigntarget::where('tbranch',$getBranch)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');


                        if($tdata)
                        {

                              if($targetdata < $paumentdats)
                             {
                                         foreach($stshid as $tid)
                                         {

                                            $tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',0)->orderBy('id','DESC')->first();

                                             $stargetdata = $tdata->totaltargets;
                                             //dd($stargetdata);
                                             
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

                                 else
                                 {
                                    $stargetdata = 0;
                                 }
                    

                        }

                        else if($ntdadat)
                        {

                        }
                           
                        $incent = $targetdata - $paumentdats;


                        if ($namesfinds) {
                                                        $inviceid_arr = [];
                                                        foreach (
                                                            $namesfinds
                                                            as $newnamesfinds
                                                        ) {
                                                            $tis = payment::select(
                                                                "id",
                                                                "remainingamount"
                                                            )
                                                                ->where(
                                                                    "inviceid",
                                                                    $newnamesfinds->inviceid
                                                                )
                                                                ->latest("id")
                                                                ->first();
                                                            $inviceid_arr[] =
                                                                $tis->id;
                                                        }
                                                        //print_r($inviceid_arr);exit;
                                                        $inv_unique = array_unique(
                                                            $inviceid_arr
                                                        );
                                                        $totalsd = payment::select(
                                                            "remainingamount"
                                                        )
                                                            ->whereIn(
                                                                "id",
                                                                $inv_unique
                                                            )
                                                            ->get()
                                                            ->sum(
                                                                "remainingamount"
                                                            );
                                                    }

                                                    $readmissions = 0;

                                                    if ($reinvoicesdata) {
                                                        $reinviceid_arr = [];
                                                        foreach (
                                                            $reinvoicesdata
                                                            as $rnewnamesfinds
                                                        ) {
                                                            $tis = payment::select(
                                                                "id",
                                                                "remainingamount"
                                                            )
                                                                ->where(
                                                                    "reinviceid",
                                                                    $rnewnamesfinds->reinviceid
                                                                )
                                                                ->latest("id")
                                                                ->first();
                                                            $inviceid_arr[] =
                                                                $tis->id;
                                                        }
                                                        //print_r($inviceid_arr);exit;
                                                        $inv_unique = array_unique(
                                                            $reinviceid_arr
                                                        );
                                                        $retotalsd = payment::select(
                                                            "remainingamount"
                                                        )
                                                            ->whereIn(
                                                                "id",
                                                                $inv_unique
                                                            )
                                                            ->get()
                                                            ->sum(
                                                                "remainingamount"
                                                            );
                                                    }
                                                    //dd($admissions);

                                                    //$radmissions = 0;

                                                    //$totalsd = $admissions + $radmissions;

                                                    if ($reinvoicesdata) {
                                                        $readmintoalbal = $retotalsd;
                                                    } else {
                                                        $readmintoalbal = 0;
                                                    }

                                                    $totalbal = $totalsd +  $readmintoalbal;
                    
                    return view('filtercentermanager',compact('getUser','getBranch','leadsdatas','almarkusrt','branchalldata','conversionstatus','invodata','targetdata','paumentdats','incent','getBranch','getDates','getUser','totalbal'));
        }
                  

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
   
    public function store(Request $request)
    {
         $getDates = $request->startDates; 
                    
                     $getUserName = Auth::user()->id;
                     $getUserNa = Auth::user()->name;
                      //$getUserName = User::where('id',$getUser)->pluck('name');

                    $newDats = explode("-",$getDates);
                    $leadsdatas = leads::where('user_id',$getUserName)->whereYear('leaddate', $newDats[0])->whereMonth('leaddate', $newDats[1])->count();
                    $conversionstatus   = admissionprocess::where('admissionsusersid',$getUserName)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->count();
                    $invodata = admissionprocess::where('admissionsusersid',$getUserName)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->sum('invtotal');
                     $tshid = assigntarget::where('tassignuser',$getUserNa)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
                        
                                   $tdata = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$getUserNa)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->where('target_alloteds.statsus',0)->orderBy('target_alloteds.id','DESC')->first();

                                     $ntdadat = assigntarget::join('target_alloteds','target_alloteds.targetuserid','=','assigntargets.id')->where('assigntargets.tassignuser',$getUserNa)->whereYear('assigntargets.enddates', $newDats[0])->where('target_alloteds.statsus',1)->orderBy('target_alloteds.id','DESC')->first();
                                    
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


                                        $invoicesdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->whereYear('payments.paymentdate',$newDats[0])->whereMonth('payments.paymentdate',$newDats[1])->where('admissionprocesses.admissionsusersid',$getUserName)->get();
                                     //dd($invoicesdata);

                       $refuncalculcation = DB::table('refunds')->where('refunds.rformsusers',$getUserName)->whereMonth('recollectionsmonths',$newDats[1])->whereYear('recollectionsmonths',$newDats[0])->get()->sum('resettlementsamounts');

                        $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$getUserName)->whereMonth('payments.paymentdate',$newDats[1])->whereYear('payments.paymentdate',$newDats[0])->orderBy('payments.id','DESC')->get();

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
                                           



        $stshid = assigntarget::where('tassignuser',$getUserNa)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
        //dd($stshid);

         if($tdata)
                        {

                              if($targetdata < $paumentdats)
                             {
                                         foreach($stshid as $tid)
                                         {

                                            $tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',0)->orderBy('id','DESC')->first();

                                             $stargetdata = $tdata->totaltargets;
                                             //dd($stargetdata);
                                             
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

                                 else
                                 {
                                    $stargetdata = 0;
                                 }
                    

                        }

                        else if($ntdadat)
                        {

                        }
                           
                        $incent = $targetdata - $paumentdats;


                        if ($namesfinds) {
                                                        $inviceid_arr = [];
                                                        foreach (
                                                            $namesfinds
                                                            as $newnamesfinds
                                                        ) {
                                                            $tis = payment::select(
                                                                "id",
                                                                "remainingamount"
                                                            )
                                                                ->where(
                                                                    "inviceid",
                                                                    $newnamesfinds->inviceid
                                                                )
                                                                ->latest("id")
                                                                ->first();
                                                            $inviceid_arr[] =
                                                                $tis->id;
                                                        }
                                                        //print_r($inviceid_arr);exit;
                                                        $inv_unique = array_unique(
                                                            $inviceid_arr
                                                        );
                                                        $totalsd = payment::select(
                                                            "remainingamount"
                                                        )
                                                            ->whereIn(
                                                                "id",
                                                                $inv_unique
                                                            )
                                                            ->get()
                                                            ->sum(
                                                                "remainingamount"
                                                            );
                                                    }

                                                    
                                                    $totalbal = $totalsd;

                    return view('filtermarkethome',compact('leadsdatas','conversionstatus','invodata','targetdata','paumentdats','incent','getDates','getUserNa','totalbal'));

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($branch,$BrStartDate)
    {
        
             $newDats = explode("-", $branch);

       
            $tshid = assigntarget::where('tbranch',$BrStartDate)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
          //  dd($tshid);
            /*$targetdata = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();*/

            $tdata  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
               if ($tdata) 
               {

                        $targetdata = $tdata->totaltargets;
               }

               else
               {
                    $targetdata = 0;
               }
             return response()->json($targetdata);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($branch,$BrStartDate)
    {
         $newDats = explode("-", $branch);

        //$pid = admissionprocess::where('stobranches',$UserBranch)->pluck('id');
        //dd($pid);

      

            $paumentdats = payment::Where('branchs',$BrStartDate)->whereYear('paymentdate', $newDats[0])->whereMonth('paymentdate', $newDats[1])->sum('paymentreceived');
            //dd($paumentdats);
                return response()->json($paumentdats);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($branch,$BrStartDate)
    {
        $newDats = explode("-", $branch);
       $tshid = assigntarget::where('tbranch',$BrStartDate)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
          //  dd($tshid);
            /*$targetdata = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();*/

            $tdata  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
               if ($tdata) 
               {

                        $targetdata = $tdata->totaltargets;
               }

               else
               {
                    $targetdata = 0;
               }
       $paumentdats = payment::Where('branchs',$BrStartDate)->whereYear('paymentdate', $newDats[0])->whereMonth('paymentdate', $newDats[1])->sum('paymentreceived');
         $incent = $targetdata - $paumentdats;
       //  dd($incent);
      
        return response()->json($incent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($branch,$BrStartDate)
    {
          $newDats = explode("-", $branch);
       $tshid = assigntarget::where('tbranch',$BrStartDate)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
          //  dd($tshid);
            /*$targetdata = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();*/

            $tdata  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
               if ($tdata) 
               {

                        $targetdata = $tdata->totaltargets;
               }

               else
               {
                    $targetdata = 0;
               }
       $paumentdats = payment::Where('branchs',$BrStartDate)->whereYear('paymentdate', $newDats[0])->whereMonth('paymentdate', $newDats[1])->sum('paymentreceived');
        
         $incent = $targetdata - $paumentdats;
          if($incent != 0)
             {
               
               $print = "<h5 class='mb-1 mt-1 text-red blink-hard'>You Have Not Achieved Target</h5>";
                return response()->json($print);
                    
             }

             else
             {
                

                 $print = "<h5 class='mb-1 mt-1 text-green blink-hard'>You Achieved Target</h5><p class='text-muted mb-0'>";
                 return response()->json($print);
             }
    }
}
