<?php

namespace App\Http\Controllers;
use App\invoices;
use App\students;
use App\Branch;
use App\course;
use App\invoicescourses;
use App\invoicesinstallmentfees;
use App\payment;
use App\leads;
use App\Source;
use App\coursecategory;
use App\followup;
use App\User;
use App\Tax;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\ReAdmission;
use App\Readmissioncourses;
use App\readmissioninstallmentfees;
use App\InstallmentFollowups;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Mail;
use PDF;
use Auth;

class CentreManagerInstallmentReminderController extends Controller
{
    
    /*function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
        $reference_array = array();

        foreach($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        array_multisort($reference_array, $direction, true);
    }*/


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $userBranch = Auth::user()->branchs;
        $currentMonth = date('m');
         $date = Carbon::now();
        $date->addDays(1);
        $getdate = $date->toDateString();

        $today = date('Y-m-d');

        

            //$getdatas = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->groupBy('payments.inviceid')->orderBy('payments.nexamountdate','DESC')->get(); 

                              $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();

             






              $all_data = [];
                foreach($getdatas_ss as $key=> $installdatas){



                                                

                                                 $currentMonth = date('m');
                                                  $todday = date('Y-m-d');
                                                  $paymentsreceived = DB::table('payments')->where('inviceid',$installdatas->admid)->sum('paymentreceived');

                                                    $cvrucoursedetails = DB::table('admissionprocesscourses')->join('courses','courses.id','=','admissionprocesscourses.univecoursid')->selectRaw('GROUP_CONCAT(coursename) as course_name')->where('admissionprocesscourses.invid',$installdatas->admid)->groupBy('admissionprocesscourses.invid')->first();


                                                            $bitcoursedetails = DB::table('admissionprocesscourses')->join('courses','courses.id','=','admissionprocesscourses.courseid')->selectRaw('GROUP_CONCAT(coursename) as bitcourse_name')->where('admissionprocesscourses.invid',$installdatas->admid)->groupBy('admissionprocesscourses.invid')->first();

                                                     $geLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','DESC')->first();
                                                     

                                                     $lrsgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','DESC')->first();
                                                    
                                                     $ersgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','asc')->first();

                                                     $ims = $installdatas->remainingamount - $paymentsreceived;
                                                        //dd($ims);
                                                    
                                                    
                                                     $getallpaymentsreceived = DB::table('payments')->where('inviceid',$installdatas->admid)->sum('paymentreceived');
                                                   
                                                           
                                                  $insdates = explode('-',$geLatestam->nexamountdate);

                                                  $emisgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->first();


                                                 // $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->skip(1)->take(1)->first();
                                                  $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->skip(1)->take(1)->orderBy('id','DESC')->first();
                                                
                                                  $treemirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->first();
                                                

                                            
    
                                               
                                            if($installdatas->invtotal != $getallpaymentsreceived && $installdatas->droppedstats != '1'){

                                                $all_data[$key]['admid'] = $installdatas->admid;
                                                $all_data[$key]['nexamountdate'] = $lrsgeLatestam->nexamountdate;
                                                $all_data[$key]['serno'] = $installdatas->serno;
                                                $all_data[$key]['studentname'] = $installdatas->studentname;
                                                if($cvrucoursedetails){
                                                        
                                                        $all_data[$key]['course_name'] = $cvrucoursedetails->course_name;
                                                }else{
                                                        $all_data[$key]['course_name'] = '';

                                                }
                                                if($bitcoursedetails){
                                                        
                                                        $all_data[$key]['bitcourse_name'] = $bitcoursedetails->bitcourse_name;
                                                }else{
                                                        $all_data[$key]['bitcourse_name'] = '';

                                                }
                                                $all_data[$key]['invtotal'] = $installdatas->invtotal;
                                                $all_data[$key]['paymentsreceived'] = $paymentsreceived;
                                                if($emirna){
                                                    $all_data[$key]['remainingamount'] = $emirna->remainingamount;
                                                }else{
                                                    $all_data[$key]['remainingamount'] = $lrsgeLatestam->remainingamount;
                                                }
                                                    
                                                if($treemirna){
                                                    $all_data[$key]['paymentreceived_two'] = $treemirna->paymentreceived;

                                                }else{
                                                    $all_data[$key]['paymentreceived_two'] = '';

                                                }

                                                if($treemirna){
                                                     if($treemirna->paymentdate){
                                                        $all_data[$key]['paymentdate'] = $treemirna->paymentdate;
                                                    }else{
                                                        $all_data[$key]['paymentdate'] = '';

                                                    }
                                                }else{
                                                        $all_data[$key]['paymentdate'] = '';

                                                }

                                                $all_data[$key]['remainingamount_two'] = $lrsgeLatestam->remainingamount;

                                                if(date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth)
                                                {
                                                    $all_data[$key]['cmup']=' Current EMI';
                                                }
                                                else
                                                {
                                                    $all_data[$key]['cmup']='UpComing';
                                                }

                                                $all_data[$key]['semails'] = $installdatas->semails;
                                                $all_data[$key]['sphone'] = $installdatas->sphone;
                                            }
                                                      
                                              
                                    }



                        $all_data_new = array_values($all_data);



            

              $getdatas = collect($all_data_new)->sortBy('nexamountdate')->reverse()->all();   

             // dd($getdatas);

             // echo '<pre>';
              // print_R($getdatas);exit;
            

            $regetdatas_ss = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->groupBy('payments.reinviceid')->latest('payments.nexamountdate')->get();


              $rall_data = [];
                foreach($regetdatas_ss as $key=> $installdatas){



                                                

                                                 
                                                 $currentMonth = date('m');
                                                  $todday = date('Y-m-d');
                                                  $paymentsreceived = DB::table('payments')->where('reinviceid',$installdatas->reid)->sum('paymentreceived');

                                                    $cvrucoursedetails = DB::table('readmissioncourses')->join('courses','courses.id','=','readmissioncourses.reunivecoursid')->selectRaw('GROUP_CONCAT(coursename) as course_name')->where('readmissioncourses.reinvid',$installdatas->reid)->groupBy('readmissioncourses.reinvid')->first();


                                                            $bitcoursedetails = DB::table('readmissioncourses')->join('courses','courses.id','=','readmissioncourses.recourseid')->selectRaw('GROUP_CONCAT(coursename) as bitcourse_name')->where('readmissioncourses.reinvid',$installdatas->reid)->groupBy('readmissioncourses.reinvid')->first();

                                                     $geLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','DESC')->first();
                                                     

                                                     $lrsgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','DESC')->first();
                                                    
                                                     $ersgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','asc')->first();

                                                     $ims = $installdatas->remainingamount - $paymentsreceived;
                                                        //dd($ims);
                                                    
                                                    
                                                     $getallpaymentsreceived = DB::table('payments')->where('reinviceid',$installdatas->reid)->sum('paymentreceived');
                                                   
                                                           
                                                  $insdates = explode('-',$geLatestam->nexamountdate);

                                                  $emisgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->where('studenterno',null)->orderBy('id','DESC')->first();


                                                 // $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->skip(1)->take(1)->first();
                                                  $emirna = DB::table('payments')->where('reinviceid',$installdatas->reid)->skip(1)->take(1)->orderBy('id','DESC')->first();
                                                
                                                  $treemirna = DB::table('payments')->where('reinviceid',$installdatas->reid)->where('studenterno',null)->orderBy('id','DESC')->first();
                                                

                                            
    
                                               
                                            if($installdatas->rinvtotal != $getallpaymentsreceived && $installdatas->droppedstats != '1')
                                            {

                                                $rall_data[$key]['reid'] = $installdatas->reid;
                                                $rall_data[$key]['nexamountdate'] = $lrsgeLatestam->nexamountdate;
                                                $rall_data[$key]['rserno'] = $installdatas->rserno;
                                                $rall_data[$key]['rstudents'] = $installdatas->rstudents;
                                                if($cvrucoursedetails){
                                                        
                                                        $rall_data[$key]['course_name'] = $cvrucoursedetails->course_name;
                                                }else{
                                                        $rall_data[$key]['course_name'] = '';

                                                }
                                                if($bitcoursedetails){
                                                        
                                                        $rall_data[$key]['bitcourse_name'] = $bitcoursedetails->bitcourse_name;
                                                }else{                                                    $rall_data[$key]['bitcourse_name'] = '';

                                                }
                                                $rall_data[$key]['rinvtotal'] = $installdatas->rinvtotal;
                                                $rall_data[$key]['paymentsreceived'] = $paymentsreceived;
                                                if($emirna){
                                                    $rall_data[$key]['remainingamount'] = $emirna->remainingamount;
                                                }else{
                                                    $rall_data[$key]['remainingamount'] = $lrsgeLatestam->remainingamount;
                                                }
                                                    
                                                if($treemirna){
                                                    $rall_data[$key]['paymentreceived_two'] = $treemirna->paymentreceived;

                                                }else{
                                                    $rall_data[$key]['paymentreceived_two'] = '';

                                                }

                                                if($treemirna){
                                                     if($treemirna->paymentdate){
                                                        $rall_data[$key]['paymentdate'] = $treemirna->paymentdate;
                                                    }else{
                                                        $rall_data[$key]['paymentdate'] = '';

                                                    }
                                                }else{
                                                        $rall_data[$key]['paymentdate'] = '';

                                                }

                                                $rall_data[$key]['remainingamount_two'] = $lrsgeLatestam->remainingamount;

                                                if(date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth)
                                                {
                                                    $rall_data[$key]['cmup']=' Current EMI';
                                                }
                                                else
                                                {
                                                    $rall_data[$key]['cmup']='UpComing';
                                                }

                                                $rall_data[$key]['rsemails'] = $installdatas->rsemails;
                                                $rall_data[$key]['rsphone'] = $installdatas->rsphone;
                                            }
                                                      
                                              
                                    }



                        $rall_data_new = array_values($rall_data);



            

              $regetdatas = collect($rall_data_new)->sortBy('nexamountdate')->reverse()->all();


         
       
        $folss = followup::all();

          $cour = course::all();
                 $branchdata = Branch::get();
                 $userdata = User::get();
              $sourcedata = Source::get();
              $ccatall = coursecategory::get();
       
        return view('centremanager.installmentreminder.manage',compact('getdatas','regetdatas','folss','cour','branchdata','userdata','sourcedata','ccatall'));
    }



     public function upcominginstallments()
    {
        $currentMonth = date('m');
         $date = Carbon::now();
        $date->addDays(1);
        $getdate = $date->toDateString();

        $userBranch = Auth::user()->branchs;

        $today = date('Y-m-d');

        

            //$getdatas = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->groupBy('payments.inviceid')->orderBy('payments.nexamountdate','DESC')->get(); 

                $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();

             






              $all_data = [];
                foreach($getdatas_ss as $key=> $installdatas){



                                                

                                                 $currentMonth = date('m');
                                                  $todday = date('Y-m-d');
                                                  $paymentsreceived = DB::table('payments')->where('inviceid',$installdatas->admid)->sum('paymentreceived');

                                                    $cvrucoursedetails = DB::table('admissionprocesscourses')->join('courses','courses.id','=','admissionprocesscourses.univecoursid')->selectRaw('GROUP_CONCAT(coursename) as course_name')->where('admissionprocesscourses.invid',$installdatas->admid)->groupBy('admissionprocesscourses.invid')->first();


                                                            $bitcoursedetails = DB::table('admissionprocesscourses')->join('courses','courses.id','=','admissionprocesscourses.courseid')->selectRaw('GROUP_CONCAT(coursename) as bitcourse_name')->where('admissionprocesscourses.invid',$installdatas->admid)->groupBy('admissionprocesscourses.invid')->first();

                                                     $geLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','DESC')->first();
                                                     

                                                     $lrsgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','DESC')->first();
                                                    
                                                     $ersgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','asc')->first();

                                                     $ims = $installdatas->remainingamount - $paymentsreceived;
                                                        //dd($ims);
                                                    
                                                    
                                                     $getallpaymentsreceived = DB::table('payments')->where('inviceid',$installdatas->admid)->sum('paymentreceived');
                                                   
                                                           
                                                  $insdates = explode('-',$geLatestam->nexamountdate);

                                                  $emisgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->first();


                                                 // $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->skip(1)->take(1)->first();
                                                  $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->skip(1)->take(1)->orderBy('id','DESC')->first();
                                                
                                                  $treemirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->first();
                                                

                                            
    
                                               
                                            if($installdatas->invtotal != $getallpaymentsreceived && $installdatas->droppedstats != '1' && date("m", strtotime($lrsgeLatestam->nexamountdate)) >  $currentMonth){

                                                $all_data[$key]['admid'] = $installdatas->admid;
                                                $all_data[$key]['nexamountdate'] = $lrsgeLatestam->nexamountdate;
                                                $all_data[$key]['serno'] = $installdatas->serno;
                                                $all_data[$key]['studentname'] = $installdatas->studentname;
                                                if($cvrucoursedetails){
                                                        
                                                        $all_data[$key]['course_name'] = $cvrucoursedetails->course_name;
                                                }else{
                                                        $all_data[$key]['course_name'] = '';

                                                }
                                                if($bitcoursedetails){
                                                        
                                                        $all_data[$key]['bitcourse_name'] = $bitcoursedetails->bitcourse_name;
                                                }else{
                                                        $all_data[$key]['bitcourse_name'] = '';

                                                }
                                                $all_data[$key]['invtotal'] = $installdatas->invtotal;
                                                $all_data[$key]['paymentsreceived'] = $paymentsreceived;
                                                if($emirna){
                                                    $all_data[$key]['remainingamount'] = $emirna->remainingamount;
                                                }else{
                                                    $all_data[$key]['remainingamount'] = $lrsgeLatestam->remainingamount;
                                                }
                                                    
                                                if($treemirna){
                                                    $all_data[$key]['paymentreceived_two'] = $treemirna->paymentreceived;

                                                }else{
                                                    $all_data[$key]['paymentreceived_two'] = '';

                                                }

                                                if($treemirna){
                                                     if($treemirna->paymentdate){
                                                        $all_data[$key]['paymentdate'] = $treemirna->paymentdate;
                                                    }else{
                                                        $all_data[$key]['paymentdate'] = '';

                                                    }
                                                }else{
                                                        $all_data[$key]['paymentdate'] = '';

                                                }

                                                $all_data[$key]['remainingamount_two'] = $lrsgeLatestam->remainingamount;

                                                if(date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth)
                                                {
                                                    $all_data[$key]['cmup']=' Current EMI';
                                                }
                                                else
                                                {
                                                    $all_data[$key]['cmup']='UpComing';
                                                }

                                                $all_data[$key]['semails'] = $installdatas->semails;
                                                $all_data[$key]['sphone'] = $installdatas->sphone;
                                            }
                                                      
                                              
                                    }



                        $all_data_new = array_values($all_data);



            

              $getdatas = collect($all_data_new)->sortBy('nexamountdate')->reverse()->all();   

             // dd($getdatas);

             // echo '<pre>';
              // print_R($getdatas);exit;
            

            $regetdatas_ss = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->groupBy('payments.reinviceid')->latest('payments.nexamountdate')->get();


              $rall_data = [];
                foreach($regetdatas_ss as $key=> $installdatas){



                                                

                                                 
                                                 $currentMonth = date('m');
                                                  $todday = date('Y-m-d');
                                                  $paymentsreceived = DB::table('payments')->where('reinviceid',$installdatas->reid)->sum('paymentreceived');

                                                    $cvrucoursedetails = DB::table('readmissioncourses')->join('courses','courses.id','=','readmissioncourses.reunivecoursid')->selectRaw('GROUP_CONCAT(coursename) as course_name')->where('readmissioncourses.reinvid',$installdatas->reid)->groupBy('readmissioncourses.reinvid')->first();


                                                            $bitcoursedetails = DB::table('readmissioncourses')->join('courses','courses.id','=','readmissioncourses.recourseid')->selectRaw('GROUP_CONCAT(coursename) as bitcourse_name')->where('readmissioncourses.reinvid',$installdatas->reid)->groupBy('readmissioncourses.reinvid')->first();

                                                     $geLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','DESC')->first();
                                                     

                                                     $lrsgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','DESC')->first();
                                                    
                                                     $ersgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','asc')->first();

                                                     $ims = $installdatas->remainingamount - $paymentsreceived;
                                                        //dd($ims);
                                                    
                                                    
                                                     $getallpaymentsreceived = DB::table('payments')->where('reinviceid',$installdatas->reid)->sum('paymentreceived');
                                                   
                                                           
                                                  $insdates = explode('-',$geLatestam->nexamountdate);

                                                  $emisgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->where('studenterno',null)->orderBy('id','DESC')->first();


                                                 // $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->skip(1)->take(1)->first();
                                                  $emirna = DB::table('payments')->where('reinviceid',$installdatas->reid)->skip(1)->take(1)->orderBy('id','DESC')->first();
                                                
                                                  $treemirna = DB::table('payments')->where('reinviceid',$installdatas->reid)->where('studenterno',null)->orderBy('id','DESC')->first();
                                                

                                            
    
                                               
                                            if($installdatas->rinvtotal != $getallpaymentsreceived && $installdatas->droppedstats != '1' && date("m", strtotime($lrsgeLatestam->nexamountdate)) >  $currentMonth){

                                                $rall_data[$key]['reid'] = $installdatas->reid;
                                                $rall_data[$key]['nexamountdate'] = $lrsgeLatestam->nexamountdate;
                                                $rall_data[$key]['rserno'] = $installdatas->rserno;
                                                $rall_data[$key]['rstudents'] = $installdatas->rstudents;
                                                if($cvrucoursedetails){
                                                        
                                                        $rall_data[$key]['course_name'] = $cvrucoursedetails->course_name;
                                                }else{
                                                        $rall_data[$key]['course_name'] = '';

                                                }
                                                if($bitcoursedetails){
                                                        
                                                        $rall_data[$key]['bitcourse_name'] = $bitcoursedetails->bitcourse_name;
                                                }else{                                                    $rall_data[$key]['bitcourse_name'] = '';

                                                }
                                                $rall_data[$key]['rinvtotal'] = $installdatas->rinvtotal;
                                                $rall_data[$key]['paymentsreceived'] = $paymentsreceived;
                                                if($emirna){
                                                    $rall_data[$key]['remainingamount'] = $emirna->remainingamount;
                                                }else{
                                                    $rall_data[$key]['remainingamount'] = $lrsgeLatestam->remainingamount;
                                                }
                                                    
                                                if($treemirna){
                                                    $rall_data[$key]['paymentreceived_two'] = $treemirna->paymentreceived;

                                                }else{
                                                    $rall_data[$key]['paymentreceived_two'] = '';

                                                }

                                                if($treemirna){
                                                     if($treemirna->paymentdate){
                                                        $rall_data[$key]['paymentdate'] = $treemirna->paymentdate;
                                                    }else{
                                                        $rall_data[$key]['paymentdate'] = '';

                                                    }
                                                }else{
                                                        $rall_data[$key]['paymentdate'] = '';

                                                }

                                                $rall_data[$key]['remainingamount_two'] = $lrsgeLatestam->remainingamount;

                                                if(date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth)
                                                {
                                                    $rall_data[$key]['cmup']=' Current EMI';
                                                }
                                                else
                                                {
                                                    $rall_data[$key]['cmup']='UpComing';
                                                }

                                                $rall_data[$key]['rsemails'] = $installdatas->rsemails;
                                                $rall_data[$key]['rsphone'] = $installdatas->rsphone;
                                            }
                                                      
                                              
                                    }



                        $rall_data_new = array_values($rall_data);



            

              $regetdatas = collect($rall_data_new)->sortBy('nexamountdate')->reverse()->all();

         
       
        $folss = followup::all();

          $cour = course::all();
                 $branchdata = Branch::get();
                 $userdata = User::get();
              $sourcedata = Source::get();
              $ccatall = coursecategory::get();
       
        return view('centremanager.installmentreminder.upcominginastallments',compact('getdatas','regetdatas','folss','cour','branchdata','userdata','sourcedata','ccatall'));
    }

    public function currentsinstallments()
    {
        $currentMonth = date('m');
         $date = Carbon::now();
        $date->addDays(1);
        $getdate = $date->toDateString();

        $today = date('Y-m-d');

        $userBranch = Auth::user()->branchs;

        

            //$getdatas = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->groupBy('payments.inviceid')->orderBy('payments.nexamountdate','DESC')->get(); 

              $getdatas_ss = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->where('payments.nexamountdate','!=',null)->groupBy('payments.inviceid')->latest('payments.nexamountdate')->get();

             






              $all_data = [];
                foreach($getdatas_ss as $key=> $installdatas){



                                                

                                                 $currentMonth = date('m');
                                                  $todday = date('Y-m-d');
                                                  $paymentsreceived = DB::table('payments')->where('inviceid',$installdatas->admid)->sum('paymentreceived');

                                                    $cvrucoursedetails = DB::table('admissionprocesscourses')->join('courses','courses.id','=','admissionprocesscourses.univecoursid')->selectRaw('GROUP_CONCAT(coursename) as course_name')->where('admissionprocesscourses.invid',$installdatas->admid)->groupBy('admissionprocesscourses.invid')->first();


                                                            $bitcoursedetails = DB::table('admissionprocesscourses')->join('courses','courses.id','=','admissionprocesscourses.courseid')->selectRaw('GROUP_CONCAT(coursename) as bitcourse_name')->where('admissionprocesscourses.invid',$installdatas->admid)->groupBy('admissionprocesscourses.invid')->first();

                                                     $geLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','DESC')->first();
                                                     

                                                     $lrsgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','DESC')->first();
                                                    
                                                     $ersgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->orderBy('id','asc')->first();

                                                     $ims = $installdatas->remainingamount - $paymentsreceived;
                                                        //dd($ims);
                                                    
                                                    
                                                     $getallpaymentsreceived = DB::table('payments')->where('inviceid',$installdatas->admid)->sum('paymentreceived');
                                                   
                                                           
                                                  $insdates = explode('-',$geLatestam->nexamountdate);

                                                  $emisgeLatestam = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->first();


                                                 // $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->skip(1)->take(1)->first();
                                                  $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->skip(1)->take(1)->orderBy('id','DESC')->first();
                                                
                                                  $treemirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->first();
                                                

                                            
    
                                               
                                            if($installdatas->invtotal != $getallpaymentsreceived && $installdatas->droppedstats != '1' && date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth){

                                                $all_data[$key]['admid'] = $installdatas->admid;
                                                $all_data[$key]['nexamountdate'] = $lrsgeLatestam->nexamountdate;
                                                $all_data[$key]['serno'] = $installdatas->serno;
                                                $all_data[$key]['studentname'] = $installdatas->studentname;
                                                if($cvrucoursedetails){
                                                        
                                                        $all_data[$key]['course_name'] = $cvrucoursedetails->course_name;
                                                }else{
                                                        $all_data[$key]['course_name'] = '';

                                                }
                                                if($bitcoursedetails){
                                                        
                                                        $all_data[$key]['bitcourse_name'] = $bitcoursedetails->bitcourse_name;
                                                }else{
                                                        $all_data[$key]['bitcourse_name'] = '';

                                                }
                                                $all_data[$key]['invtotal'] = $installdatas->invtotal;
                                                $all_data[$key]['paymentsreceived'] = $paymentsreceived;
                                                if($emirna){
                                                    $all_data[$key]['remainingamount'] = $emirna->remainingamount;
                                                }else{
                                                    $all_data[$key]['remainingamount'] = $lrsgeLatestam->remainingamount;
                                                }
                                                    
                                                if($treemirna){
                                                    $all_data[$key]['paymentreceived_two'] = $treemirna->paymentreceived;

                                                }else{
                                                    $all_data[$key]['paymentreceived_two'] = '';

                                                }

                                                if($treemirna){
                                                     if($treemirna->paymentdate){
                                                        $all_data[$key]['paymentdate'] = $treemirna->paymentdate;
                                                    }else{
                                                        $all_data[$key]['paymentdate'] = '';

                                                    }
                                                }else{
                                                        $all_data[$key]['paymentdate'] = '';

                                                }

                                                $all_data[$key]['remainingamount_two'] = $lrsgeLatestam->remainingamount;

                                                if(date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth)
                                                {
                                                    $all_data[$key]['cmup']=' Current EMI';
                                                }
                                                else
                                                {
                                                    $all_data[$key]['cmup']='UpComing';
                                                }

                                                $all_data[$key]['semails'] = $installdatas->semails;
                                                $all_data[$key]['sphone'] = $installdatas->sphone;
                                            }
                                                      
                                              
                                    }



                        $all_data_new = array_values($all_data);



            

              $getdatas = collect($all_data_new)->sortBy('nexamountdate')->reverse()->all();   

             // dd($getdatas);

             // echo '<pre>';
              // print_R($getdatas);exit;
            

            $regetdatas_ss = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->groupBy('payments.reinviceid')->latest('payments.nexamountdate')->get();


              $rall_data = [];
                foreach($regetdatas_ss as $key=> $installdatas){



                                                

                                                 
                                                 $currentMonth = date('m');
                                                  $todday = date('Y-m-d');
                                                  $paymentsreceived = DB::table('payments')->where('reinviceid',$installdatas->reid)->sum('paymentreceived');

                                                    $cvrucoursedetails = DB::table('readmissioncourses')->join('courses','courses.id','=','readmissioncourses.reunivecoursid')->selectRaw('GROUP_CONCAT(coursename) as course_name')->where('readmissioncourses.reinvid',$installdatas->reid)->groupBy('readmissioncourses.reinvid')->first();


                                                            $bitcoursedetails = DB::table('readmissioncourses')->join('courses','courses.id','=','readmissioncourses.recourseid')->selectRaw('GROUP_CONCAT(coursename) as bitcourse_name')->where('readmissioncourses.reinvid',$installdatas->reid)->groupBy('readmissioncourses.reinvid')->first();

                                                     $geLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','DESC')->first();
                                                     

                                                     $lrsgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','DESC')->first();
                                                    
                                                     $ersgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->orderBy('id','asc')->first();

                                                     $ims = $installdatas->remainingamount - $paymentsreceived;
                                                        //dd($ims);
                                                    
                                                    
                                                     $getallpaymentsreceived = DB::table('payments')->where('reinviceid',$installdatas->reid)->sum('paymentreceived');
                                                   
                                                           
                                                  $insdates = explode('-',$geLatestam->nexamountdate);

                                                  $emisgeLatestam = DB::table('payments')->where('reinviceid',$installdatas->reid)->where('studenterno',null)->orderBy('id','DESC')->first();


                                                 // $emirna = DB::table('payments')->where('inviceid',$installdatas->admid)->where('studenterno',null)->orderBy('id','DESC')->skip(1)->take(1)->first();
                                                  $emirna = DB::table('payments')->where('reinviceid',$installdatas->reid)->skip(1)->take(1)->orderBy('id','DESC')->first();
                                                
                                                  $treemirna = DB::table('payments')->where('reinviceid',$installdatas->reid)->where('studenterno',null)->orderBy('id','DESC')->first();
                                                

                                            
    
                                               
                                            if($installdatas->rinvtotal != $getallpaymentsreceived && $installdatas->droppedstats != '1' && date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth){

                                                $rall_data[$key]['reid'] = $installdatas->reid;
                                                $rall_data[$key]['nexamountdate'] = $lrsgeLatestam->nexamountdate;
                                                $rall_data[$key]['rserno'] = $installdatas->rserno;
                                                $rall_data[$key]['rstudents'] = $installdatas->rstudents;
                                                if($cvrucoursedetails){
                                                        
                                                        $rall_data[$key]['course_name'] = $cvrucoursedetails->course_name;
                                                }else{
                                                        $rall_data[$key]['course_name'] = '';

                                                }
                                                if($bitcoursedetails){
                                                        
                                                        $rall_data[$key]['bitcourse_name'] = $bitcoursedetails->bitcourse_name;
                                                }else{                                                    $rall_data[$key]['bitcourse_name'] = '';

                                                }
                                                $rall_data[$key]['rinvtotal'] = $installdatas->rinvtotal;
                                                $rall_data[$key]['paymentsreceived'] = $paymentsreceived;
                                                if($emirna){
                                                    $rall_data[$key]['remainingamount'] = $emirna->remainingamount;
                                                }else{
                                                    $rall_data[$key]['remainingamount'] = $lrsgeLatestam->remainingamount;
                                                }
                                                    
                                                if($treemirna){
                                                    $rall_data[$key]['paymentreceived_two'] = $treemirna->paymentreceived;

                                                }else{
                                                    $rall_data[$key]['paymentreceived_two'] = '';

                                                }

                                                if($treemirna){
                                                     if($treemirna->paymentdate){
                                                        $rall_data[$key]['paymentdate'] = $treemirna->paymentdate;
                                                    }else{
                                                        $rall_data[$key]['paymentdate'] = '';

                                                    }
                                                }else{
                                                        $rall_data[$key]['paymentdate'] = '';

                                                }

                                                $rall_data[$key]['remainingamount_two'] = $lrsgeLatestam->remainingamount;

                                                if(date("m", strtotime($lrsgeLatestam->nexamountdate)) <=  $currentMonth)
                                                {
                                                    $rall_data[$key]['cmup']=' Current EMI';
                                                }
                                                else
                                                {
                                                    $rall_data[$key]['cmup']='UpComing';
                                                }

                                                $rall_data[$key]['rsemails'] = $installdatas->rsemails;
                                                $rall_data[$key]['rsphone'] = $installdatas->rsphone;
                                            }
                                                      
                                              
                                    }



                        $rall_data_new = array_values($rall_data);



            

              $regetdatas = collect($rall_data_new)->sortBy('nexamountdate')->reverse()->all(); 




         
       
        $folss = followup::all();

          $cour = course::all();
                 $branchdata = Branch::get();
                 $userdata = User::get();
              $sourcedata = Source::get();
              $ccatall = coursecategory::get();
       
        return view('centremanager.installmentreminder.currentinstallment',compact('getdatas','regetdatas','folss','cour','branchdata','userdata','sourcedata','ccatall'));
    }

    public function dropstatewnw($droppid)
    {  // dd('chel');
        $currendatewaw = date('Y-m-d');

       /* $update = payment::whereIn('inviceid',$droppid)->update(['droppedstats' => '1','droppedatesa'=> $currendatewaw]);*/
       $update = payment::where('inviceid','=', $droppid)
                             ->update([
                                     'droppedstats' => 1,
                                     'droppedatesa'=> $currendatewaw
                               ]);
       /*$update = payment::where('inviceid',$droppid)->get();
       $update->droppedstats = 1;
       $update->droppedatesa = $currendatewaw;
       $update->save();*/

         return response()->json(
                    [
                        'success' => true,
                        'message' => 'Installment Move in Dropp'
                    ]
                ); 

    }

    public function droplists()
    {
       $getdatas = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->orderBy('payments.nexamountdate','ASC')
             ->get();

        $folss = followup::all(); 

        return view('centremanager.installmentreminder.dropplist',compact('getdatas','folss'));
    }

    public function filterinsatllmentreminder(Request $request)
    {

        $userbranch =  Auth::user()->branchs;
                $datesfor = "";
        $namedatas = "";
        $mobdatas = "";
        $coursedatas = "";
        $cmodes = "";
        $sources= "";
        $fsearch = "";
        $asearch = "";
        $bransdata = "";
        $categorydata = "";

        if($namedatas = $request->getstudentsnames)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
           $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

         // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->orderBy('payments.paymentdate','DESC')->get(); 

              $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
             ->groupBy('payments.inviceid')
             ->get();
        

          return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
        // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orderBy('payments.paymentdate','DESC')->get(); 

         $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)
              ->groupBy('payments.inviceid')
             ->get();

       

          return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Installment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

              // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->whereBetween('payments.nexamountdate',[$startdates,$enddats])
             ->orderBy('payments.nexamountdate','DESC')
              ->groupBy('payments.inviceid')
             ->get();
               

                return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            //  $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->whereBetween('payments.paymentdate',[$startdates,$enddats])
             ->orderBy('payments.paymentdate','DESC')
              ->groupBy('payments.inviceid')
             ->get();
               

                return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();

         //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

         $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
            ->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
             ->groupBy('payments.inviceid')
             ->get();
         

          return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }

     /* elseif($cmodes = $request->CourseModeSearch)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.coursesmode',$cmodes)->orderBy('leads.id','DESC')->get();
         foreach($namesfinds as $leas)
                             {
                                 $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                 $leas->followupstatus ='';
                                 $leas->takenby ='';
                                 $leas->flfollwpdate ='';
                                 $leas->flremarsk = '';
                                 $leas->nxtfollowupdate = '';

                                 if($da)
                                 {
                                     $leas->followupstatus = $da->followupstatus;
                                     $leas->takenby = $da->takenby;
                                     $leas->flfollwpdate = $da->flfollwpdate;
                                     $leas->flremarsk = $da->flremarsk;
                                     $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                    
                                 }

                               }

          return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }*/


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        

         // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.paymentdate','DESC')->get(); 

           $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
             ->groupBy('payments.inviceid')
             ->get();
         

          return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }



      /*elseif($fsearch = $request->FollowupsSearch)
      {
         $fdates = $request->fsdate;
         $fenddates = $request->fedate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->orderBy('leadsfollowups.nxtfollowupdate','DESC')->get();
               
               foreach($namesfinds as $leas)
                                   {
                                       $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                       $leas->followupstatus ='';
                                       $leas->takenby ='';
                                       $leas->flfollwpdate ='';
                                       $leas->flremarsk = '';
                                       $leas->nxtfollowupdate = '';

                                       if($da)
                                       {
                                           $leas->followupstatus = $da->followupstatus;
                                           $leas->takenby = $da->takenby;
                                           $leas->flfollwpdate = $da->flfollwpdate;
                                           $leas->flremarsk = $da->flremarsk;
                                           $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                          
                                       }

                              }

                return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }*/



      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

  

          //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
               
             $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])
             ->groupBy('payments.inviceid')
             ->get();

                return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

          //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->orderBy('payments.paymentdate','DESC')->get(); 

          $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
             ->groupBy('payments.inviceid')
             ->get();
               
             

                return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
      }


      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
           //dd($findcourse);

           /* foreach($findcourse as $courses)
            {
                  $getourses = $courses->coursename;

            }*/

          //  dd($findcourse);

      

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

        // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.paymentdate','DESC')->get(); 

         $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
            ->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
             ->groupBy('payments.inviceid')
             ->get();
               
              

                return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      } 

    }


    public function completedinstallment(Request $request)
    {
        $ssearch = $request->startdatess;
        $esearch = $request->enddatess;
        $currentMonth = date('m');
       //  $today = date('Y-m-d');

       $getdatas =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->where('payments.nexamountdate','!=',null)
             ->orderBy('payments.nexamountdate','DESC')
             ->groupBy('payments.inviceid')
             ->get();

             foreach($getdatas as $studentpaymen)
            {
                $das = payment::where('inviceid',$studentpaymen->admid)->orderBy('id','DESC')->first();

                
                $studentpaymen->remainingamount = '';
                $studentpaymen->nexamountdate = '';
                
                
                 if($das){
                    $studentpaymen->remainingamount = $das->remainingamount;
                    $studentpaymen->nexamountdate = $das->nexamountdate;
                    
                    
                }
            }

        return view('centremanager.installmentreminder.completedinstallment',compact('getdatas')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $getsids = $request->getinstid;
       // dd($getsids);
            $mergids = implode(",",$getsids);
            //dd($mergids);
        //$mainid = admissionprocessinstallmentfees::find($id); 
         $data = array();
         $date = Carbon::now();
        $date->addDays(1);
        $getdate = $date->toDateString();

         $getadmissionsdetails = admissionprocess::whereIn('id',$request->getinstid)->get();

           // dd($getadmissionsdetails);


            foreach($getadmissionsdetails as $key => $dtasysl)
            {
         

             $getreminderdata = payment::where('inviceid',$dtasysl->id)->orderBy('id','DESC')->first();

                $data["StudentsName"] = $dtasysl->studentname;
                $data["Studentserno"] = $dtasysl->serno;
                $data["StudentInvoiceNo"] = "";
                $data["studentsemails"] = $dtasysl->semails;  
                $data["Iamount"] = $getreminderdata->remainingamount;
                $data["installdate"] =  date('d-m-Y',strtotime($getreminderdata->nexamountdate));
                             
                Mail::send('centremanager.installmentreminder.installmentreminder', $data, function ($message) use ($data) {
                    $data;
                    $message->to($data["studentsemails"],$data["studentsemails"])
                        ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                        ->cc('support@bitbaroda.com','Admission BIT')
                        ->subject("Your Installment is due on (". $data["installdate"].")");

                });
            // $emails = ['myoneemail@esomething.com', 'myother@esomething.com','myother2@esomething.com'];

         
                if (Mail::failures()) {
                            dd('mailerror');
                        } 
                 }
        
         return redirect()->back()->with('success','Installment Reminder Sent Successfully in Mail !!!');

       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {

         $today = date('Y-m-d');
           $getadmissionsdetails = admissionprocess::find($id);
        $getremindernotsdata = \DB::table('admissionprocesses')->Join('admissionprocessinstallmentfees', 'admissionprocessinstallmentfees.invoid', '=', 'admissionprocesses.id')->whereDate('admissionprocessinstallmentfees.invoicedate','<=',$today)->select('admissionprocesses.*','admissionprocesses.id as aid','admissionprocessinstallmentfees.*')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocessinstallmentfees.id = payments.installmentid');
                                                            })->first();
        $data["StudentsName"] = $getadmissionsdetails->studentname;
        $data["Studentserno"] = $getadmissionsdetails->serno;
        $data["StudentInvoiceNo"] = $getadmissionsdetails->Invoiceno;
        $data["studentsemails"] = $getadmissionsdetails->semails;
        $data["Iamount"] = $getremindernotsdata->installmentamount;
        $data["installdate"] =  date('d-m-Y',strtotime($getremindernotsdata->invoicedate));
                     
        Mail::send('centremanager.installmentreminder.installmentreminder', $data, function ($message) use ($data) {
            $data;
            $message->to($data["studentsemails"],$data["studentsemails"])
                ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Your Installment is due on (". $data["installdate"].")");

        });

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect()->back()->with('success','Installment Reminder Sent Successfully in Mail !!!');

                }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
          $data = $request->all();
          $result = InstallmentFollowups::insert($data);
                
                 return response()->json(
                    [
                        'success' => true,
                        'message' => 'Followups Done successfully'
                    ]
                ); 
    }
    public function installmentfollowusp(Request $request)
    {
         $adnuias = $request->admissionids;
        $data= array();
        $result = InstallmentFollowups::where('admissionsfrom','=',$adnuias)->orderBy('id','DESC')->get();
        //dd($result);
        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->afollowupsstatus;
            $row[] = date('d-m-Y',strtotime($res->afollowupsdate));
            $row[] = $res->afollowupsremarks;
            $row[] = date('d-m-Y',strtotime($res->anextfollowupsdate));
            $row[] = $res->afollowupsby;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
         
         $userBranch = Auth::user()->branchs;

       $getdatas =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->where('payments.nexamountdate','!=',NULL)
             ->where('admissionprocesses.stobranches',$userBranch)
             ->orderBy('payments.id','DESC')
             ->groupBy('payments.inviceid')
             ->get();

             

        $regetdatas = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->where('payments.nexamountdate','!=',NULL)->groupBy('payments.reinviceid')->latest('payments.nexamountdate')->get();


          $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        return view('centremanager.installmentreminder.installmentlist',compact('getdatas','regetdatas','folss','userdata','branchdata','ccatall','cour','sourcedata'));
    }


    public function filterbymonth(Request $request)
      {

        $datesfor = "";
        $namedatas = "";
        $mobdatas = "";
        $coursedatas = "";
        $cmodes = "";
        $sources= "";
        $fsearch = "";
        $asearch = "";
        $bransdata = "";
        $categorydata = "";

        if($namedatas = $request->getstudentsnames)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

         // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->orderBy('payments.paymentdate','DESC')->get(); 

             

             $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('payments.nexamountdate','!=',null)
         ->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
         ->groupBy('payments.inviceid')
         ->get();
        

          return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
        // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orderBy('payments.paymentdate','DESC')->get(); 

        

              $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('payments.nexamountdate','!=',null)
          ->Where('admissionprocesses.sphone', $mobdatas)
          ->orwhere('admissionprocesses.swhatsappno',$mobdatas)
         ->groupBy('payments.inviceid')
         ->get();

       

          return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Installment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

              // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 
            /*
                $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->whereBetween('payments.nexamountdate',[$startdates,$enddats])
             ->orderBy('payments.nexamountdate','DESC')
             ->get();*/

              $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('payments.nexamountdate','!=',null)
        ->whereBetween('payments.nexamountdate',[$startdates,$enddats])
        ->orderBy('payments.nexamountdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();
               

                return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            //  $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

               /* $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->whereBetween('payments.paymentdate',[$startdates,$enddats])
             ->orderBy('payments.paymentdate','DESC')
             ->get();*/

             $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('payments.nexamountdate','!=',null)
        ->whereBetween('payments.paymentdate',[$startdates,$enddats])
        ->orderBy('payments.paymentdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();
               

                return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();

         //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

        /* $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
            ->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
             ->get();*/

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
         ->where('payments.nexamountdate','!=',null)
         ->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
        ->orderBy('payments.paymentdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();
         

          return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }

     /* elseif($cmodes = $request->CourseModeSearch)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.coursesmode',$cmodes)->orderBy('leads.id','DESC')->get();
         foreach($namesfinds as $leas)
                             {
                                 $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                 $leas->followupstatus ='';
                                 $leas->takenby ='';
                                 $leas->flfollwpdate ='';
                                 $leas->flremarsk = '';
                                 $leas->nxtfollowupdate = '';

                                 if($da)
                                 {
                                     $leas->followupstatus = $da->followupstatus;
                                     $leas->takenby = $da->takenby;
                                     $leas->flfollwpdate = $da->flfollwpdate;
                                     $leas->flremarsk = $da->flremarsk;
                                     $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                    
                                 }

                               }

          return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }*/


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        

         // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.paymentdate','DESC')->get(); 

          /* $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
             ->get();*/


             $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
        ->orderBy('payments.paymentdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();
         

          return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }



      /*elseif($fsearch = $request->FollowupsSearch)
      {
         $fdates = $request->fsdate;
         $fenddates = $request->fedate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->orderBy('leadsfollowups.nxtfollowupdate','DESC')->get();
               
               foreach($namesfinds as $leas)
                                   {
                                       $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                       $leas->followupstatus ='';
                                       $leas->takenby ='';
                                       $leas->flfollwpdate ='';
                                       $leas->flremarsk = '';
                                       $leas->nxtfollowupdate = '';

                                       if($da)
                                       {
                                           $leas->followupstatus = $da->followupstatus;
                                           $leas->takenby = $da->takenby;
                                           $leas->flfollwpdate = $da->flfollwpdate;
                                           $leas->flremarsk = $da->flremarsk;
                                           $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                          
                                       }

                              }

                return view('centremanager.installmentreminder.filterinstallment',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }*/



      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

  

          //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
               
             $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])
             ->get();

               $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.nexamountdate',[$asdates,$aenddates])
        ->orderBy('payments.paymentdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();

                return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

          //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->orderBy('payments.paymentdate','DESC')->get(); 

          /*$namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
             ->get();*/

             $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.nexamountdate',[$bstartdate,$benddate])
        ->orderBy('payments.paymentdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();
               
             

                return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
      }


      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
          

               $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as ppids')
         ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
         ->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
        ->orderBy('payments.paymentdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();
               
              

                return view('centremanager.installmentreminder.filterinstallmentlist',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      } 

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       // $emails = implode(",",$request->getemails);
       
       $emails =  implode(",",$request->getemails);

       $memails = explode(",",$emails);

       $getid = admissionprocess::where('semails',$memails)->pluck('id');
       $instid = admissionprocessinstallmentfees::where('invoid',$getid)->pluck('id');


       DB::table('admissionprocessinstallmentfees')->whereIn('id', $instid)->update(array('bstatus' => 1));

        $getadmissionsdetails = admissionprocess::find($getid);
        

         
       // dd($getadmissionsdetails->studentname);
        $getremindernotsdata = \DB::table('admissionprocesses')->Join('admissionprocessinstallmentfees', 'admissionprocessinstallmentfees.invoid', '=', 'admissionprocesses.id')->select('admissionprocesses.*','admissionprocesses.id as aid','admissionprocessinstallmentfees.*')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocessinstallmentfees.id = payments.installmentid');
                                                            })->first();
                     $data["Iamount"] = $getremindernotsdata->installmentamount;
                     $data["installdate"] =  date('d-m-Y',strtotime($getremindernotsdata->invoicedate));
                    foreach($getadmissionsdetails  as $key => $getadmissions)
                    {
                              $data["StudentsName"] = $getadmissions->studentname;
                                $data["Studentserno"] = $getadmissions->serno;
                                $data["StudentInvoiceNo"] = $getadmissions->Invoiceno;
                                $data["studentsemails"] = $getadmissions->semails;

                                //dd($data["studentsemails"]);

                                Mail::send('centremanager.installmentreminder.installmentreminder', $data, function ($message) use ($data) {
                                    $data;
                                    $message->to($data["studentsemails"],$data["StudentsName"])
                                        ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                                        ->cc('support@bitbaroda.com','Admission BIT')
                                        ->subject("Your Installment is due on (". $data["installdate"].")");

                                });
                    }

      
       
                     
        

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect()->back()->with('success','Installment Reminder Sent Successfully in Mail !!!');

                }
    

        //dd($getadmissionsdetails);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
          
    }  

} 
