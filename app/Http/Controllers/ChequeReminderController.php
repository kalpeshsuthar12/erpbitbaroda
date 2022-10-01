<?php

namespace App\Http\Controllers;
use App\invoices;
use App\students;
use App\Branch;
use App\course;
use App\invoicescourses;
use App\invoicesinstallmentfees;
use App\payment;
use App\TargetAlloted;
use App\assigntarget;
use App\IncentiveReleasePayments;
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
use App\Paymenthistory;
use App\PaymentSource;
use App\ExpenseCategory;
use App\cheque_followups;
use App\User_Salary_Deductions;
use App\SalaryCalculations;
use App\ChequeAgainstMoney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Mail;
use PDF;
use Auth;

class ChequeReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentMonth = date('m');
         $date = Carbon::now();
        $date->addDays(1);
        $getdate = $date->toDateString();

        $today = date('Y-m-d');

        /*dd($today);*/
        
      
        $getreminderdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
         ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->where('payments.paymentmode', '=', 'Bank (Cheque)')
          ->where('payments.chequedate', '=', $getdate)
          ->orderBy('payments.chequedate','DESC')
          ->get();

         $getremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
         ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->where('payments.paymentmode', '=', 'Bank (Cheque)')
          ->where('payments.chequedate', '<=', $today)
          ->orderBy('payments.chequedate','DESC')
          ->get();

           $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        return view('superadmin.chequereminder.manage',compact('getreminderdata','getremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall'));
    }
    
    public function chequefollowups(Request $request)
    {
        $data = $request->all();
        
        $result = cheque_followups::insert($data);
                
                 return response()->json(
                    [
                        'success' => true,
                        'message' => 'Followups Done successfully'
                    ]
                ); 
    }

    public function getchequefollowupsdata(Request $request)
    {
         $adnuias = $request->admissionids;
        $data= array();
        $result = cheque_followups::where('cadmissionsfrom','=',$adnuias)->orderBy('id','DESC')->get();
        //dd($result);
        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->cafollowupsstatus;
            $row[] = date('d-m-Y',strtotime($res->cafollowupsdate));
            $row[] = $res->cafollowupsremarks;
            $row[] = date('d-m-Y',strtotime($res->canextfollowupsdate));
            $row[] = $res->cafollowupsby;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }
    
    public function chequedroppstatus($chdroppid)
    { 
         $chequwsstatus = payment::find($chdroppid);
         $chequwsstatus->chequedroppstatus = 1;
         $chequwsstatus->chequestatus = 1;
         $chequwsstatus->save();

         return response()->json(
                    [
                        'success' => true,
                        'message' => 'Cheque Move in Dropp'
                    ]
                ); 




    }
    
    public function chequecollectsdatas($chcolid)
    { 
         $chequwcollectsstatus = payment::find($chcolid);
         $chequwcollectsstatus->chequescollecdates = date('Y-m-d');
         $chequwcollectsstatus->save();

         return response()->json(
                    [
                        'success' => true,
                        'message' => 'Cheque Collected Successfully!'
                    ]
                ); 




    }

    public function filtersbychequereminder(Request $request)
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
        $chequenos="";
        $bnames="";


        $today = date('Y-m-d');

                    $currentMonth = date('m');
                    $date = Carbon::now();
                    $date->addDays(1);
                    $getdate = $date->toDateString();
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

              

               $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                     ->Where('admissionprocesses.studentname','like', '%' .$namedatas. '%')
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                      ->Where('admissionprocesses.studentname','like', '%' .$namedatas. '%')
                      ->orderBy('payments.chequedate','DESC')
                      ->get();
        

         return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
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

         /*$namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)
             ->get();*/

              $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                     ->Where('admissionprocesses.sphone', $mobdatas)
                     ->orwhere('admissionprocesses.swhatsappno',$mobdatas)
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                     ->Where('admissionprocesses.sphone', $mobdatas)
                     ->orwhere('admissionprocesses.swhatsappno',$mobdatas)
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

       

         return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Cheque Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

              // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                /*$namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->whereBetween('payments.nexamountdate',[$startdates,$enddats])
             ->orderBy('payments.nexamountdate','DESC')
             ->get();*/

              $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                     ->whereBetween('payments.chequedate',[$startdates,$enddats])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                     ->whereBetween('payments.chequedate',[$startdates,$enddats])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();
               

                return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','startdates','enddats'));
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

                /*$namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->whereBetween('payments.paymentdate',[$startdates,$enddats])
             ->orderBy('payments.paymentdate','DESC')
             ->get();*/

             $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                     ->whereBetween('payments.paymentdate',[$startdates,$enddats])
                      ->orderBy('payments.paymentdate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                     
                      ->where('payments.chequedate', '<=', $today)
                     ->whereBetween('payments.paymentdate',[$startdates,$enddats])
                      ->orderBy('payments.paymentdate','DESC')
                      ->get();
               

                return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','startdates','enddats'));
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

         /*$namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
            ->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
             ->get();*/

             $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                     ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                     ->where('payments.chequedate', '=', $getdate)
                      ->where('admissionprocesscourses.courseid',$coursedatas)
                      ->orWhere('admissionprocesscourses.univecoursid',$coursedatas)
                      ->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                     ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                      ->where('admissionprocesscourses.courseid',$coursedatas)
                      ->orWhere('admissionprocesscourses.univecoursid',$coursedatas)
                      ->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();
         

          return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','cstartsdates','cendsdates'));
      }

     
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

        

         

               $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                      ->where('admissionprocesses.admsisource',$sources)
                       ->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                       ->where('admissionprocesses.admsisource',$sources)
                       ->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();
         

          return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','starsdates','enssdates'));
      }



      



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
               
            /* $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])
             ->get();*/

             $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                     ->where('admissionprocesses.admissionsusersid',$asearch)
                     ->whereBetween('payments.paymentdate',[$asdates,$aenddates])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                       ->where('admissionprocesses.admissionsusersid',$asearch)
                       ->whereBetween('payments.paymentdate',[$asdates,$aenddates])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','asdates','aenddates'));
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

        

             $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                    ->where('admissionprocesses.stobranches',$bransdata)
                    ->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                       ->where('admissionprocesses.stobranches',$bransdata)
                       ->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();
               
             

                return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','bstartdate','benddate'));
      }


      elseif($chequenos = $request->chequeno)
      {
         

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

            $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                      ->where('payments.chequeno', 'like', '%' .$chequenos. '%')
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                      ->where('payments.chequeno', 'like', '%' .$chequenos. '%')
                      ->orderBy('payments.chequedate','DESC')
                      ->get();


               
             

                return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
      }

      elseif($bnames = $request->banksnames)
      {
         

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        


                       $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '=', $getdate)
                     ->where('payments.bankname', 'like', '%' .$bnames. '%')
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                     ->where('payments.bankname', 'like', '%' .$bnames. '%')
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                
               
             

                return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
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
         
              $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                     ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                     ->where('payments.chequedate', '=', $getdate)
                     ->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();

                     $cremindernotsdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                     ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequedate', '<=', $today)
                       ->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
                      ->orderBy('payments.chequedate','DESC')
                      ->get();
               
              

                return view('superadmin.chequereminder.filterchequereminder',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','cstartdate','cenddate'));
      } 

    }

      public function show($id)
    {
         $data = array();
         $date = Carbon::now();
        $date->addDays(1);
        $getdate = $date->toDateString();

        $paymentsis  = payment::find($id); 

            $paymentsis->buttonstatus = 1;
            $paymentsis->save();

         $getadmissionsdetails = admissionprocess::find($paymentsis->inviceid);
         $getreminderdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')
         ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->where('admissionprocesses.id', '=', $getadmissionsdetails->id)
         ->first();
         //dd($getreminderdata);

        
        $data["StudentsName"] = $getadmissionsdetails->studentname;
        $data["Studentserno"] = $getadmissionsdetails->serno;
        $data["StudentInvoiceNo"] =$getreminderdata->chequeno;
        $data["studentsemails"] = $getadmissionsdetails->semails;
        $data["Iamount"] = $getreminderdata->paymentreceived;
        $data["installdate"] =  date('d-m-Y',strtotime($getreminderdata->chequedate));
                     
        Mail::send('superadmin.chequereminder.chequereminder', $data, function ($message) use ($data) {
            $data;
            $message->to($data["studentsemails"],$data["studentsemails"])
                ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("PDC Cheque Reminder");

        });

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect()->back()->with('success','Cheque Reminder Sent Successfully in Mail !!!');

                }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id,Request $request)
    {
        $getid = payment::find($id);
        $paymentdetails = admissionprocess::find($getid->inviceid);
        $branc = Branch::all();
        $installmentfees = admissionprocessinstallmentfees::where('invoid',$getid->inviceid)->where('status',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
      
        return view('superadmin.chequereminder.create',compact('paymentdetails','branc','installmentfees','getid','psource'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store($id,Request $request)
    {
        $update = payment::find($id);

        $currentdate = date('d-m-Y');
       // dd($currentdate);

        $Paymenthistorymodel = new Paymenthistory();
        $Paymenthistory = $Paymenthistorymodel->create([
            'paymentinvoiceid'=> $update->inviceid,
            'paymentid'=> $id,
            'ppaymentmode'=> $update->paymentmode,
            'pbankname'=> $update->bankname,
            'pchequeno'=> $update->chequeno,
            'pchequedate'=> $update->chequedate,
            'pchequedepositto'=> $update->chequedepositsto,
            
        ]);


        if($request->paymentmode == 'Bank (Cheque)')
        {
            $ChequeAgainstMoneymodel = new ChequeAgainstMoney();
                            $ChequeAgainstMoney = $ChequeAgainstMoneymodel->create([
                        'cacpid'=> $id,
                        'cacpaymodes'=> $request->paymentmode,
                        'cactotalamounts'=> $request->gtotalamount,
                        'cacpayableamounts'=> $request->gpayableamount,
                        'cacremainingamounts'=> $request->gremainingamount,
                        'cacbanknames'=> $request->chequedate,
                        'cacchequenos'=> $request->chequeno,
                        'cacchequtyoe'=> $request->chequetype,
                        'cacchequedates'=> $request->chequedate,
                        'cacpaymentdates'=> $request->paymentsdates,
                        'cacnextamountdates'=> $request->gnxtamountdate,
                        'cacremarks'=> $request->gremarks,
                        
                    ]);

                              $update->chequestatus = 1;
                              $update->save();

        }

        else
        {

            $ChequeAgainstMoneymodel = new ChequeAgainstMoney();
                            $ChequeAgainstMoney = $ChequeAgainstMoneymodel->create([
                        'cacpid'=> $id,
                        'cacpaymodes'=> $request->paymentmode,
                        'cactotalamounts'=> $request->gtotalamount,
                        'cacpayableamounts'=> $request->gpayableamount,
                        'cacremainingamounts'=> $request->gremainingamount,
                        'cacpaymentdates'=> $request->paymentsdates,
                        'cacnextamountdates'=> $request->gnxtamountdate,
                        'cacremarks'=> $request->gremarks,
                        
                    ]);

                            $update->chequestatus = 1;
                              $update->save();

        }

            
           // return redirect('/revised-payment-receipt/'.$id)->with('success','Cheque Details Change Successfully');

        //return redirect('/bank-accounting-details/'.$update->paymentdate)->with('success','Cheque Details Change Successfully');
        
         return redirect('/bank-accounting-details/'.$update->paymentdate.'/'.$update->branchs)->with('success','Cheque Details Change Successfully');
             
        

       

         /* return redirect('/manage-cheque-list')->with('success','Cheque Details Change Successfully');*/
    }


     public function repaymentreceipt($id)
    {
        $selectID = payment::find($id);
            $newId = $selectID->inviceid;

        $aprocess = admissionprocess::find($newId);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$newId.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$newId.'" ');

         $paymentdata = payment::where('inviceid',$newId)->first();

         $makepayment = DB::select('SELECT * FROM  admissionprocesses a, payments p WHERE a.id = p.inviceid AND a.id = "'.$newId.'" ');

         /*$installmentdata = DB::SELECT('SELECT * FROM  admissionprocesses a, payments p WHERE a.id = p.inviceid AND a.id = "'.$id.'" ');*/

         /*$installdata = DB::select("SELECT * FROM admissionprocessinstallmentfees f, admissionprocesses a, payments p  WHERE a.id = p.inviceid AND a.id = f.invoid AND a.id = '$id' ORDER BY f.id DESC");*/

         $installdata = admissionprocessinstallmentfees::leftJoin('payments', 'payments.installmentid', '=', 'admissionprocessinstallmentfees.id')->where('admissionprocessinstallmentfees.invoid',$newId)->orderBy('admissionprocessinstallmentfees.id','DESC')->get();        
         

        

         /*$payments = payment::where('inviceid',$id)->first();*/

        // dd($payments);
      //   dd($payments);

        
        

        return view('superadmin.chequereminder.repaymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $pid = $request->paymentid;
        $data= array();
        $result = payment::where('inviceid',$pid)->where('paymentmode','Bank (Cheque)')->orderBy('id','DESC')->get();
        
        
        
      
            
            foreach($result as $res)
            {
                $row = array();
                $row[] = $res->paymentmode;
                $row[] = $res->bankname;
                $row[] = $res->chequeno;
                $row[] = date('d-m-Y',strtotime($res->chequedate));
                $row[] = $res->paymentreceived;

                if($res->chequedroppstatus == null && $res->chequestatus == 1)
                {
                    $row[] =  "<div class='badge bg-soft-success font-size-12'>Clear</div>";
                }
                else
                {
                     $row[] = "<input type='checkbox' name='ind' id='chs' onchange='ChequeDroppStatus(".$res->id.")'>";
                }
               
                $row[] = "<a href='/reset-cheque-details/".$res->id."'>Reset</a>";
                
               


                if($res->chequestatus == 0 && $res->paymentmode == 'Bank (Cheque)')
                {
                      $row[] = "<a href='/make-cheque-clear/".$res->id."' class='btn btn-warning'>Make Cheque Clear</a>"; 

                    
                }
               else if($res->chequedroppstatus == 1 && $res->chequestatus == 1)
                {
                     $row[] =  "<div class='badge bg-soft-danger font-size-12'>Cancelled (CH)</div>";
                }

                else if($res->chequestatus == 0 && $res->paymentmode != 'Bank (Cheque)')
                {
                     $row[] =  "<div class='badge bg-soft-success font-size-12'>Payment Mode Change To ".$res->paymentmode."</div>";
                }
                else
                {
                 $row[] =  "<div class='badge bg-soft-success font-size-12'>Clear</div>";
                }

                
                           if($res->chequestatus == 0 && $res->paymentmode == 'Bank (Cheque)')
                            {
                                $row[] =  "<a href='/change-cheque-details/".$res->id."' class='btn btn-primary'>Change Cheque Status</a>";  
                            }
                            else if($res->chequestatus == 0 && $res->paymentmode != 'Bank (Cheque)')
                            {
                                 $row[] =  "<div class='badge bg-soft-success font-size-12'>Payment Mode Change To ".$res->paymentmode."</div>";
                            }

                             else if($res->chequedroppstatus == 1  && $res->chequestatus == 1)
                            {
                                 $row[] =  "<div class='badge bg-soft-danger font-size-12'>Cheque Cancel</div>";
                            }
                            else
                            {
                                 $row[] =  "<div class='badge bg-soft-success font-size-12'>Clear</div>";
                            }
                            
                $row[] = $res->chequedepositsto;

               if($res->chequedroppstatus == 1  && $res->chequestatus == 1)
                            {
                                 $row[] =  "<div class='badge bg-soft-danger font-size-12'>Cheque Cancel</div>";
                            }
               else
               {
                  $row[] = "<button class='btn btn-warning' onclick='GetCheckHistory(".$res->inviceid.")'>Cheque Log</button>";

               }

                $data[] = $row;
            }

             $response = array(
                "recordsTotal"    => count($data),  
                "recordsFiltered" => count($data), 
                "data"            => $data   
             );

             echo json_encode($response);
        
              
        

         

    }
    
     public function  readmissionscheque(Request $request)
    {
        $pid = $request->readpaymentid;
        $data= array();
        $result = payment::where('reinviceid',$pid)->where('paymentmode','Bank (Cheque)')->orderBy('id','DESC')->get();
        
        
        
      
            
            foreach($result as $res)
            {
                $row = array();
                $row[] = $res->paymentmode;
                $row[] = $res->bankname;
                $row[] = $res->chequeno;
                $row[] = date('d-m-Y',strtotime($res->chequedate));
                $row[] = $res->paymentreceived;

                if($res->chequedroppstatus == null && $res->chequestatus == 1)
                {
                    $row[] =  "<div class='badge bg-soft-success font-size-12'>Clear</div>";
                }
                else
                {
                     $row[] = "<input type='checkbox' name='ind' id='chs' onchange='ChequeDroppStatus(".$res->id.")'>";
                }
               
                $row[] = "<a href='/reset-cheque-details/".$res->id."'>Reset</a>";
                
               


                if($res->chequestatus == 0 && $res->paymentmode == 'Bank (Cheque)')
                {
                      $row[] = "<a href='/make-cheque-clear/".$res->id."' class='btn btn-warning'>Make Cheque Clear</a>"; 

                    
                }
               else if($res->chequedroppstatus == 1 && $res->chequestatus == 1)
                {
                     $row[] =  "<div class='badge bg-soft-danger font-size-12'>Cancelled (CH)</div>";
                }

                else if($res->chequestatus == 0 && $res->paymentmode != 'Bank (Cheque)')
                {
                     $row[] =  "<div class='badge bg-soft-success font-size-12'>Payment Mode Change To ".$res->paymentmode."</div>";
                }
                else
                {
                 $row[] =  "<div class='badge bg-soft-success font-size-12'>Clear</div>";
                }

                
                           if($res->chequestatus == 0 && $res->paymentmode == 'Bank (Cheque)')
                            {
                                $row[] =  "<a href='/change-cheque-details/".$res->id."' class='btn btn-primary'>Change Cheque Status</a>";  
                            }
                            else if($res->chequestatus == 0 && $res->paymentmode != 'Bank (Cheque)')
                            {
                                 $row[] =  "<div class='badge bg-soft-success font-size-12'>Payment Mode Change To ".$res->paymentmode."</div>";
                            }

                             else if($res->chequedroppstatus == 1  && $res->chequestatus == 1)
                            {
                                 $row[] =  "<div class='badge bg-soft-danger font-size-12'>Cheque Cancel</div>";
                            }
                            else
                            {
                                 $row[] =  "<div class='badge bg-soft-success font-size-12'>Clear</div>";
                            }
                            
                $row[] = $res->chequedepositsto;

               if($res->chequedroppstatus == 1  && $res->chequestatus == 1)
                            {
                                 $row[] =  "<div class='badge bg-soft-danger font-size-12'>Cheque Cancel</div>";
                            }
               else
               {
                  $row[] = "<button class='btn btn-warning' onclick='GetCheckHistory(".$res->reinviceid.")'>Cheque Log</button>";

               }

                $data[] = $row;
            }

             $response = array(
                "recordsTotal"    => count($data),  
                "recordsFiltered" => count($data), 
                "data"            => $data   
             );

             echo json_encode($response);
        
    }

    public function chequehistory(Request $request)
    {
        $cheqieuid = $request->chqids;
        $data= array();
         $chequesdlogs = Paymenthistory::join('payments','payments.id','=','paymenthistories.paymentid')->select('paymenthistories.*','payments.*')->where('paymenthistories.paymentinvoiceid',$cheqieuid)->get();

          foreach($chequesdlogs as $res)
            {
                $row = array();
                $row[] = $res->ppaymentmode;
                $row[] = $res->pbankname;
                $row[] = $res->pchequeno;
                $row[] = date('d-m-Y',strtotime($res->pchequedate));
                 if($res->chequedepositsto)
                           {
                            $row[] =   $res->chequedepositsto;
                           } 
                        elseif($res->revisedpaymentsmodes)
                              {
                                 $row[] =  $res->revisedpaymentsmodes."<br> <label>CAC Date</label> ".date('d-m-Y',strtotime($res->rdatess));
                              }
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function update()
    {
        $userBranchs = Auth::user()->branchs;
        
        $getreminderdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
         ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->where('payments.paymentmode', '=', 'Bank (Cheque)')
          ->where('payments.branchs', '=', $userBranchs)
         ->orderBy('payments.chequedate','DESC')
         ->get();


         $rereminderdata = payment::select('re_admissions.*','payments.*','re_admissions.id as reid','payments.id as pid')
         ->Join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->where('payments.paymentmode', '=', 'Bank (Cheque)')
           ->where('payments.branchs', '=', $userBranchs)
         ->orderBy('payments.chequedate','DESC')
         ->get();

           $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

           return view('superadmin.chequereminder.managechecklist',compact('rereminderdata','getreminderdata','folss','userdata','cour','sourcedata','branchdata','ccatall'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function chequeclear($id)
    {
        $chequeclear = payment::find($id);
        $ex = ExpenseCategory::all();
        return view('superadmin.chequereminder.chequeclear',compact('chequeclear','ex'));
    }
    
    
    
    public function resetcheque($id)
    {
        $chequeclear = payment::find($id);
         $chequeclear->chequedepositsto = NULL;
        $chequeclear->chequeremarsk = NULL;
        $chequeclear->chequestatus = NULL;
        $chequeclear->save(); 

        return redirect()->back()->with('success','Cheque Reset Successfully');
    }

     public function updatechequeclear($id,Request $request)
    {
         $update = payment::find($id);
          $Paymenthistorymodel = new Paymenthistory();
        $Paymenthistory = $Paymenthistorymodel->create([
            'paymentinvoiceid'=> $update->inviceid,
            'paymentid'=> $id,
            'ppaymentmode'=> $update->paymentmode,
            'pbankname'=> $update->bankname,
            'pchequeno'=> $update->chequeno,
            'pchequedate'=> $update->chequedate,
            'pchequedepositto'=> $update->chequedepositsto,
            
        ]);

         $salarys = $request->chequamounts;


         /*if($request->usersId != null && str_contains($request->ccchequedepositsto, 'Salary'))
                        {
                            //dd("exist!!!");

                             $datas = explode('-',$request->datesofex);

                              $scalutions = SalaryCalculations::whereMonth('datesofsalarys',$datas[1])->whereYear('datesofsalarys',$datas[0])->where('user_details_id',$request->usersId)->latest()->first();
                              $deductedamounts = User_Salary_Deductions::where('salssalarysid',$scalutions->id)->latest()->first();

                                $pendingsala = $deductedamounts->salspendingsalarys;
                                $salarys = $request->chequamounts;
                                    $finasalal = $pendingsala - $salarys;
                              
                                    //dd($finasalal);
                                    if($finasalal < 0)
                                    {

                                        return redirect()->back()->with('error','Sorry Cheque Amount and Salary Amount Not Matched!!');

                                    }

                                    else
                                    {

                                        $CashExpense = new User_Salary_Deductions([
                                                   
                                                    'salssalarysid'   => $scalutions->id,
                                                    'salsusersid'   => $scalutions->user_details_id,
                                                    'salsworkingsalarys'   => $scalutions->users_salarys,
                                                    'salsfinalsalarys'   => $scalutions->uwrkingsalary,
                                                    'totalrealeasesalary'   => $deductedamounts->salspaidsalarys,
                                                    'salspaidsalarys'   => $salarys,
                                                    'salspendingsalarys'   => $finasalal,
                                                    'salspaymentdate'   => $request->datesofex,
                                                    'salspaymoddes'   => "Bank (Cheque)",
                                                    'smonthsdatas'   => $deductedamounts->smonthsdatas,
                                                    
                                                    
                                                ]);
                                                $CashExpense->save();

                                    }


                              

                                //dd($scalutions);
                        }


         if($request->usersId != null && str_contains($request->ccchequedepositsto, 'Incentive'))
         {


             $getUsersCatgory = User::find($request->usersId);

                                        if($getUsersCatgory->usercategory == 'Marketing')
                                        {

                                                                        $months = explode('-',$request->datesofex);

                                                                        $ernrollmentfees = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->where("payments.studenterno","!=",null)->where('admissionprocesses.admissionsusersid',$request->usersId)->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->get();

                                                                 $ss_sum = 0;
                                                                foreach($ernrollmentfees as $students)
                                                                {
                                                                        $cvrufeessdetaiks = DB::table('cvru_fees')->where('studentid',$students->pids)->first();

                                                                         $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                         if($students->studenterno != null)
                                                                                {

                                                                                     if($getrefunds)
                                                                                        {

                                                                                        }

                                                                                     else
                                                                                     {

                                                                                              if($cvrufeessdetaiks)
                                                                                              {



                                                                                                     if($cvrufeessdetaiks->cvrufees != 0)

                                                                                                     {
                                                                                                          //abs($cvrufeessdetaiks->cvrufees - $students->paymentreceived) }}

                                                                                                           
                                                                                                        $ss_sum  += abs($cvrufeessdetaiks->cvrufees - $students->paymentreceived);
                                                                                                        
                                                                                                    }
                                                                                                    else

                                                                                                       {

                                                                                                        $ss_sum  += $students->paymentreceived;
                                                                                                       

                                                                                                       } //{{ $students->paymentreceived }}

                                                                                                         
                                                                                                        

                                                                                                     
                                                                                             }
                                                                                              else
                                                                                              {
                                                                                                 //{{  $students->paymentreceived }}
                                                                                                   
                                                                                                        $ss_sum  += $students->paymentreceived;
                                                                                                        
                                                                                            }

                                                                                     }

                                                                                            

                                                                                                        

                                                                                }

                                                                                            
                                                                }

                                                                 
                                                                
                                                                
                                                                $installmentfees = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->where("payments.studenterno",null)->where('admissionprocesses.admissionsusersid',$request->usersId)->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->get();

                                                              

                                                               $ins_sum = 0;
                                                                foreach($installmentfees as $insstudents)
                                                                {
                                                                        $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$insstudents->pids)->select('refunds.*')->first();

                                                                        $cvrufeessdetaiks = DB::table('cvru_fees')->where('studentid',$insstudents->pids)->first();


                                                                         if($insstudents->studenterno == null && $insstudents->inviceid)
                                                                         {          

                                                                                    if($regetrefunds)
                                                                                    {

                                                                                    }
                                                                                    else
                                                                                    {

                                                                                        if($cvrufeessdetaiks)
                                                                                              {

                                                                                                     if($cvrufeessdetaiks->cvrufees != 0)

                                                                                                     {
                                                                                                        
                                                                                                        $ins_sum  += abs($cvrufeessdetaiks->cvrufees - $insstudents->paymentreceived);
                                                                                                    }
                                                                                                      
                                                                                                    else
                                                                                                    {
                                                                                                        
                                                                                                        $ins_sum  += $insstudents->paymentreceived;
                                                                                                    }
                                                                                                        
                                                                                              }
                                                                                              else
                                                                                              {
                                                                                                 
                                                                                                          $ins_sum  += $insstudents->paymentreceived;
                                                                                              }
                                                                                                   

                                                                                    }
                                                                                              
                                                                                              
                                                                                             
                                                                        }
                                                                             
                                                                                
                                                                }

                                                                    
                                                                   
                                                                  $getreminderdata =  payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                                                                         ->join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                                                                          ->where('admissionprocesses.admissionsusersid',$request->usersId)
                                                                          ->where('payments.chequetype','PDC Cheque')
                                                                          ->whereMonth('payments.paymentdate', '=',$months[1])
                                                                          ->whereYear('payments.paymentdate', '=', $months[0])
                                                                         ->orderBy('payments.paymentdate','DESC')
                                                                         ->get();

                                                                    $apdccollections = 0;
                                                                            foreach($getreminderdata as $noreminders)
                                                                            {

                                                                                $apdccollections += $noreminders->paymentreceived;
                                                                            }


                                                                        

                                                                           

                                                                   $pdccollections  = $apdccollections;


                                                                  $paumentdats = $ins_sum + $ss_sum ;


                                                                    $getUsername = User::find($usid[$i]);

                                                                  $stshid = assigntarget::where('tassignuser',$getUsername->name)->whereYear('enddates', $months[0])->whereMonth('enddates', $months[1])->pluck('id');
                                                                
                                                              foreach($stshid as $tid)
                                                                 {  
                                                                    $tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',1)->orderBy('id','DESC')->first();
                                                                    
                                                                    $ntdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',0)->orderBy('id','DESC')->first();


                                                                        if($tdata)
                                                                        {
                                                                            $totaltargets = $tdata->totaltargets;
                                                                            $insentives = $tdata->incentive;
                                                                        }
                                                                        else if($ntdata)
                                                                        {
                                                                             $totaltargets = $ntdata->totaltargets;
                                                                            $insentives = $ntdata->incentive;
                                                                        }


                                                                    }

                                                                       
                                                                  $val = abs($pdccollections - $paumentdats);
                                                                  $ince = $insentives;

                                                                  $totalicen =  $val * $ince / 100;

                                                                  $remaincent = $totalicen - $salarys;


                                                                  $IncentiveReleasePaymentsmodel = new IncentiveReleasePayments();
                                                                $IncentiveReleasePayments = $IncentiveReleasePaymentsmodel->create([
                                                                    'incentcollections'=> $val,
                                                                    'mincentivs'=> $insentives.'%',
                                                                    'payableincentivespayments'=> $salarys,
                                                                    'remainingincentives'=> $remaincent,
                                                                    'incpaymentsmodes'=> 'Bank (Cheque)',
                                                                    'incentivespaymentsdates'=> $request->datesofex,
                                                                    'iusersids'=> $usid[$i],
                                                                    'mothsof'=> $request->datesofex,
                                                                ]);


                                        }


                                        if($getUsersCatgory->usercategory == 'Centre Manager')
                                        {

                                                 $months = explode('-',$request->datesofex);

                                                                                                       $ernrollmentfees = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->where("payments.studenterno","!=",null)->where('admissionprocesses.stobranches',$getUsersCatgory->branchs)->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->get();

                                                                 $ss_sum = 0;
                                                                foreach($ernrollmentfees as $students)
                                                                {
                                                                        $cvrufeessdetaiks = DB::table('cvru_fees')->where('studentid',$students->pids)->first();

                                                                         $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$students->pids)->select('refunds.*')->first();

                                                                         if($getrefunds)
                                                                         {

                                                                         }
                                                                         else
                                                                         {

                                                                            if($students->studenterno != null)
                                                                                {

                                                                                              if($cvrufeessdetaiks)
                                                                                              {



                                                                                                     if($cvrufeessdetaiks->cvrufees != 0)

                                                                                                     {
                                                                                                          //abs($cvrufeessdetaiks->cvrufees - $students->paymentreceived) }}

                                                                                                           
                                                                                                        $ss_sum  += abs($cvrufeessdetaiks->cvrufees - $students->paymentreceived);
                                                                                                        
                                                                                                    }
                                                                                                    else

                                                                                                       {

                                                                                                        $ss_sum  += $students->paymentreceived;
                                                                                                       

                                                                                                       } //{{ $students->paymentreceived }}

                                                                                                         
                                                                                                        

                                                                                                     
                                                                                             }
                                                                                              else
                                                                                              {
                                                                                                 //{{  $students->paymentreceived }}
                                                                                                   
                                                                                                        $ss_sum  += $students->paymentreceived;
                                                                                                        
                                                                                            }

                                                                                                        

                                                                                        }
                                                                         }

                                                                         

                                                                                            
                                                                }

                                                                 $reernrollmentfees = payment::join('re_admissions','re_admissions.id','=','payments.reinviceid')->select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->where('re_admissions.rstobranches',$getUsersCatgory->branchs)->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->get();
                                                
                                                    
                                                                    $rins_sum  = 0;
                                                                    foreach($reernrollmentfees as $reinsdatas)
                                                                    {

                                                                            $cvrufeessdetaiks = DB::table('cvru_fees')->where('studentid',$reinsdatas->pids)->first();

                                                                            $regetrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$reinsdatas->pids)->select('refunds.*')->first();

                                                                                 if($reinsdatas->reinviceid)
                                                                                              
                                                                                 {

                                                                                    if($regetrefunds)
                                                                                    {

                                                                                    }
                                                                                    else
                                                                                    {

                                                                                            if($cvrufeessdetaiks)
                                                                                               {


                                                                                                     if($cvrufeessdetaiks->cvrufees != 0)
                                                                                                     {

                                                                                                        $rins_sum   += abs($cvrufeessdetaiks->cvrufees - $reinsdatas->paymentreceived);
                                                                                                     }    
                                                                                                        

                                                                                                    else
                                                                                                    {
                                                                                                        $rins_sum  += $reinsdatas->paymentreceived;

                                                                                                    }
                                                                                                          
                                                                                                       

                                                                                                 }  

                                                                                              else
                                                                                                {

                                                                                                    $rins_sum  += $reinsdatas->paymentreceived;
                                                                                                }
                                                                                                        


                                                                                    }


                                                                                              
                                                                                             
                                                                                             

                                                                                                       
                                                                                               

                                                                                   
                                                                                }
                                                                                     
                                                                                

                                                                    }
                                                                
                                                                
                                                                $installmentfees = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->where("payments.studenterno",null)->where('admissionprocesses.stobranches',$getUsersCatgory->branchs)->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->get();

                                                              

                                                               $ins_sum = 0;
                                                                foreach($installmentfees as $insstudents)
                                                                {

                                                                        $cvrufeessdetaiks = DB::table('cvru_fees')->where('studentid',$insstudents->pids)->first();

                                                                          $getrefunds = payment::join('refunds','refunds.ressttlemenstspaymentsid','=','payments.id')->where('refunds.ressttlemenstspaymentsid',$insstudents->pids)->select('refunds.*')->first();


                                                                         if($insstudents->studenterno == null && $insstudents->inviceid)
                                                                         {

                                                                            if($getrefunds)
                                                                            {

                                                                            }

                                                                            else
                                                                            {

                                                                                 if($cvrufeessdetaiks)
                                                                                              {

                                                                                                     if($cvrufeessdetaiks->cvrufees != 0)

                                                                                                     {
                                                                                                        
                                                                                                        $ins_sum  += abs($cvrufeessdetaiks->cvrufees - $insstudents->paymentreceived);
                                                                                                    }
                                                                                                      
                                                                                                    else
                                                                                                    {
                                                                                                        
                                                                                                        $ins_sum  += $insstudents->paymentreceived;
                                                                                                    }
                                                                                                        
                                                                                              }
                                                                                              else
                                                                                              {
                                                                                                 
                                                                                                          $ins_sum  += $insstudents->paymentreceived;
                                                                                              }

                                                                            }
                                                                                       
                                                                                             
                                                                                                        
                                                                                              
                                                                                             
                                                                        }
                                                                             
                                                                                
                                                                }

                                                                    $apdccollections = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$getUsersCatgory->branchs)->where('payments.chequetype','PDC Cheque')->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->sum('payments.paymentreceived');
                                                                   
                                                                   $repdccollections = payment::join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$getUsersCatgory->branchs)->where('payments.chequetype','PDC Cheque')->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->sum('payments.paymentreceived');

                                                                   $pdccollections  = $apdccollections + $repdccollections;


                                                                  $paumentdats = $ins_sum + $ss_sum + $rins_sum;


                                                                 $stshid = assigntarget::where('tbranch',$getUsersCatgory->branchs)->whereYear('enddates', $months[0])->whereMonth('enddates', $months[1])->pluck('id');
                                                                
                                                              foreach($stshid as $tid)
                                                                 {  
                                                                    $tdata  = TargetAlloted::where('targetuserid',$tid)->where('statsus',1)->orderBy('id','DESC')->first();


                                                                       // $totaltargets = $tdata->totaltargets;
                                                                        $insentives = $tdata->incentive;
                                                                  }


                                                                  $val = abs($pdccollections - $paumentdats);
                                                                  $ince = $insentives;

                                                                  $totalicen =  $val * $ince / 100;


                                                                  $remaincent = $totalicen - $salarys;


                                                                  $IncentiveReleasePaymentsmodel = new IncentiveReleasePayments();
                                                                $IncentiveReleasePayments = $IncentiveReleasePaymentsmodel->create([
                                                                    'incentcollections'=> $val,
                                                                    'mincentivs'=> $insentives.'%',
                                                                    'payableincentivespayments'=> $salarys,
                                                                    'remainingincentives'=> $remaincent,
                                                                    'incpaymentsmodes'=> 'Bank (Cheque)',
                                                                    'incentivespaymentsdates'=> $request->datesofex,
                                                                    'ibranchs'=> $getUsersCatgory->branchs,
                                                                    'mothsof'=> $request->datesofex,
                                                                ]);


                                        }
         }
*/
       
        $update->chequedepositsto = $request->ccchequedepositsto;
        $update->chequeremarsk = $request->remarks;
        $update->chequestatus = 1;
        $update->psusersId = $request->usersId;
        $update->save(); 

       // return redirect('/bank-accounting-details/'.$update->paymentdate.'/'.$update->branchs)->with('success','Cheque Clear Successfully');
        return redirect()->back()->with('success','Cheque Clear Successfully');
    }



     public function filtersby(Request $request)
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
        $chequenos="";
        $bnames="";
        $today = date('Y-m-d');
        
                    $currentMonth = date('m');
                    $date = Carbon::now();
                    $date->addDays(1);
                    $getdate = $date->toDateString();
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

              

               $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->Where('admissionprocesses.studentname','like', '%' .$namedatas. '%')
                     ->orderBy('payments.chequedate','DESC')
                     ->get();
                     
        

         return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
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

         /*$namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)
             ->get();*/

             

                      $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->Where('admissionprocesses.sphone', $mobdatas)
                     ->orwhere('admissionprocesses.swhatsappno',$mobdatas)
                      ->orderBy('payments.chequedate','DESC')
                     ->get();

                    

       

         return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Cheque Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

              // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                /*$namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid')
             ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->whereBetween('payments.nexamountdate',[$startdates,$enddats])
             ->orderBy('payments.nexamountdate','DESC')
             ->get();*/

            

                    $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                       ->whereBetween('payments.chequedate',[$startdates,$enddats])
                     ->orderBy('payments.chequedate','DESC')
                     ->get();
               

                return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            
             

                     $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                       ->whereBetween('payments.paymentdate',[$startdates,$enddats])
                        ->orderBy('payments.chequedate','DESC')
                     ->get();
               

                return view('superadmin.chequereminder.filtercheques',compact('namesfinds','cremindernotsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','startdates','enddats'));
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
         

             

                  $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.id')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                       ->where('admissionprocesscourses.courseid',$coursedatas)
                      ->orWhere('admissionprocesscourses.univecoursid',$coursedatas)
                      ->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
                      ->orderBy('payments.chequedate','DESC')
                     ->get();
         

          return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','cstartsdates','cendsdates'));
      }

    

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

                

                     $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                        ->where('admissionprocesses.admsisource',$sources)
                       ->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
                      ->orderBy('payments.chequedate','DESC')
                     ->get();

          return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','starsdates','enssdates'));
      }


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

  

       
            
                     $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                       ->where('admissionprocesses.admissionsusersid',$asearch)
                     ->whereBetween('payments.paymentdate',[$asdates,$aenddates])
                      ->orderBy('payments.chequedate','DESC')
                     ->get();

                return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','asdates','aenddates'));
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


             

                     $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('admissionprocesses.stobranches',$bransdata)
                    ->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
                      ->orderBy('payments.chequedate','DESC')
                     ->get();
               
             

                return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','bstartdate','benddate'));
      }


      elseif($chequenos = $request->chequeno)
      {
         

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

      

          

                      $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('payments.chequeno', 'like', '%' .$chequenos. '%')
                      ->orderBy('payments.chequedate','DESC')
                     ->get();


               
             

                return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
      }

      elseif($bnames = $request->banksnames)
      {
         

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        
                       $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                     ->where('payments.bankname', 'like', '%' .$bnames. '%')
                     ->orderBy('payments.chequedate','DESC')
                     ->get();
                     

                
               
             

                return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames'));
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
           

      

       

                     $namesfinds = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
                     ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                     ->Join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')
                      ->where('payments.paymentmode', '=', 'Bank (Cheque)')
                      ->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
                      ->orderBy('payments.chequedate','DESC')
                     ->get();
               
              

                return view('superadmin.chequereminder.filtercheques',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','chequenos','bnames','cstartdate','cenddate'));
      } 

    }
    
  

}
