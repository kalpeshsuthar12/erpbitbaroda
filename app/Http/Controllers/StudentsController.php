<?php

namespace App\Http\Controllers;
use App\students;
use App\course;
use App\Branch;
use App\leads;
use App\payment;
use App\studentscourse;
use App\Tax;
use App\User;
use App\Source;
use App\admissionprocess;
use App\followup;
use App\coursecategory;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\ReAdmission;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\UnviersitiesCategory;
use App\universititiesfeeslist;
use Illuminate\Http\Request;
use Auth;
use DB;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getstudentsaccountdetails($id)
    {
         $invoiceid  = admissionprocess::find($id);
         $paymentsid = payment::where('inviceid',$id)->orderBy('id','DESC')->first();
          return redirect('/paymentreceipt/'.$paymentsid->id);
    }
    
    
    public function alldatas()
    {
         

            $studentsdata = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();




         


           $cour = course::all();
         $branchdata = Branch::get();
         $userdata = User::get();
      $sourcedata = Source::get();
      $ccatall = coursecategory::get();

         return view('superadmin.student.alldatas',compact('studentsdata','cour','branchdata','userdata','sourcedata','ccatall'));
    }

   public function filteralldatas(Request $request)
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

          //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->groupBy('payments.inviceid')->orderBy('admissionprocesses.sadate','DESC')->get(); 

            $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->orWhere('re_admissions.rstudents', 'like', '%' .$namedatas. '%')->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();
        

          return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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


            $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orwhere('re_admissions.rsphone',$mobdatas)->orwhere('re_admissions.rswhatsappno   ',$mobdatas)->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();
        
       //  $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->groupBy('payments.inviceid')->orderBy('admissionprocesses.sadate','DESC')->get(); 

       

          return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

              // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('admissionprocesses.sadate','DESC')->get(); 

               $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();
               

                return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

              $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();
               

                return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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

         //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('admissionprocesses.sadate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 


         $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->leftjoin('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->orWhere('readmissioncourses.recourseid',$coursedatas)->orWhere('readmissioncourses.reunivecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();
         

          return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
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

        

          $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->where('admissionprocesses.admsisource',$sources)->orwhere('re_admissions.radmsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();
         

          return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
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

  

         // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('admissionprocesses.sadate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

          $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pid')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get();
         
               
             

                return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
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

             //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('admissionprocesses.sadate',[$bstartdate,$benddate])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 


             $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->where('admissionprocesses.stobranches',$bransdata)->orwhere('re_admissions.rstobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get();
               
             

                return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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
            

         //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('admissionprocesses.sadate',[$cstartdate,$cenddate])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get();


          $namesfinds = payment::leftjoin('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->leftjoin('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->leftjoin('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')->select('admissionprocesses.*','re_admissions.*','payments.*','admissionprocesses.id as aid','re_admissions.id as rid','payments.id as pid')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->orWhere('readmissioncourses.recourseid',$findcourse)->orWhere('readmissioncourses.reunivecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid','payments.reinviceid')->orderBy('payments.id','DESC')->get(); 
               
              

                return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      }

   }

    public function index(students $students)
    {
        
        /*$studentsdata = admissionprocess::get();*/
        
        $userBranch = Auth::user()->branchs;

        $brnagch = Branch::all();
        $userALl = User::all();
             $currentMonth = date('m');
         $studentsdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereMonth('payments.paymentdate',$currentMonth)->where('payments.studenterno','!=',null)->where('admissionprocesses.stobranches',$userBranch)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

       
         foreach($studentsdata as $studentpaymen)
         {
            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }

         }

          $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as reid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('payments.paymentdate',$currentMonth)->where('re_admissions.rstobranches',$userBranch)->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
         foreach($newstudentsdata as $studentpaymen)
         {
            $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }

         }


         $invoicesdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();
        
        $reinovicesdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as remid')->join('payments','payments.reinviceid','=','re_admissions.id')->whereMonth('payments.paymentdate',$currentMonth)->where('re_admissions.rstobranches',$userBranch)->orderBy('payments.id','DESC')->get();


         $invototal = $invoicesdata->sum('invtotal');
        
        $retotal = $reinovicesdata->sum('rinvtotal');
          

         $sumtotal =  $invototal +  $retotal;

         $pamenreceived = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereMonth('admissionprocesses.sadate',$currentMonth)->sum('paymentreceived'); 
         

         
         $repaymreceived = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->sum('paymentreceived'); 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;

             $cour = course::all();
         $branchdata = Branch::get();
         $userdata = User::get();
      $sourcedata = Source::get();
      $ccatall = coursecategory::get();

        return view('superadmin.student.manage',compact('studentsdata','brnagch','userALl','newstudentsdata','sumtotal','totslreceived','remainingamount','ccatall','sourcedata','userdata','branchdata','cour'));

    }


      public function findBranchwiseAdmission(Request $request)
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
          $namesfinds = "";
          $ramesfinds = "";

      if($namedatas = $request->getstudentsnames)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

          //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->groupBy('payments.inviceid')->orderBy('admissionprocesses.sadate','DESC')->get(); 

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get();

            foreach($namesfinds as $studentpaymen)
               {
                  $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                  $studentpaymen->receiptno ='';
                  $studentpaymen->paymentreceived ='';
                  $studentpaymen->remainingamount ='';
                 
                  
                   if($das){
                      $studentpaymen->receiptno = $das->receiptno;
                      $studentpaymen->paymentreceived = $das->paymentreceived;
                      $studentpaymen->remainingamount = $das->remainingamount;
                      
                      
                  }

               } 

             $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->Where('re_admissions.rstudents', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
         foreach($newstudentsdata as $studentpaymen)
         {
            $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }

         }
         
         
        

          return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
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
         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get();

            foreach($namesfinds as $studentpaymen)
               {
                  $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                  $studentpaymen->receiptno ='';
                  $studentpaymen->paymentreceived ='';
                  $studentpaymen->remainingamount ='';
                 
                  
                   if($das){
                      $studentpaymen->receiptno = $das->receiptno;
                      $studentpaymen->paymentreceived = $das->paymentreceived;
                      $studentpaymen->remainingamount = $das->remainingamount;
                      
                      
                  }

               } 


            $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->Where('re_admissions.rsphone', $mobdatas)->orwhere('re_admissions.rswhatsappno',$mobdatas)->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                  foreach($newstudentsdata as $studentpaymen)
                  {
                     $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  }
          

       

          return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

       

          if($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              //namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.studenterno','DESC')->get(); 
              
               $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


                  foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }


               

                return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats','ramesfinds'));
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

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

         foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                   


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('readmissioncourses','readmissioncourses.reinvid','=','readmissioncourses.id')->join('payments', 'payments.inviceid', '=', 'readmissioncourses.id')->where('readmissioncourses.recourseid',$coursedatas)->orWhere('readmissioncourses.reunivecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }

         

          return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates','ramesfinds'));
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

        

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

          foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.radmsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }

         

          return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates','ramesfinds'));
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

  

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get();


          foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 
          $newstudentsdata =""; 




                return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
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

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->groupBy('payments.inviceid')->orderBy('payments.studenterno','DESC')->get(); 

            foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }

               
             

                return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate','ramesfinds'));
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

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->where('payments.studenterno','!=',null)->whereBetween('payments.id',[$cstartdate,$cenddate])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 


          foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('readmissioncourses','readmissioncourses.reinvid','=','re_admissions.id')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('readmissioncourses.recourseid',$findcourse)->orWhere('readmissioncourses.reunivecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get();
       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }

               
              

                return view('superadmin.student.FilterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate','ramesfinds'));
      }

   }
    public function findBranchwisePendingAdmission(Request $request)
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

          $namesfinds = \DB::table('admissionprocesses')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }
        

           return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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
         $namesfinds = \DB::table('admissionprocesses')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }

       

           return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

               $namesfinds = \DB::table('admissionprocesses')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
               

                 return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

               $namesfinds = \DB::table('admissionprocesses')->whereBetween('payments.paymentdate',[$startdates,$enddats])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
               

                 return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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
        

        // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('admissionprocesses.sadate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

             $namesfinds = \DB::table('admissionprocesses')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('admissionprocesses.sadate',[$cstartsdates,$cendsdates])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
         

           return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
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

          return view('superadmin.student.filteralldatas',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

        

          /*$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->whereBetween('admissionprocesses.sadate',[$starsdates,$enssdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); */

           $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admsisource',$sources)->whereBetween('admissionprocesses.sadate',[$starsdates,$enssdates])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists(function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
         

           return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
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

                 return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

  

         /* $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('admissionprocesses.sadate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); */
          $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('admissionprocesses.sadate',[$asdates,$aenddates])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }
               
             

                 return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
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

          /*$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('admissionprocesses.sadate',[$bstartdate,$benddate])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); */

          $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('admissionprocesses.sadate',[$bstartdate,$benddate])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }
               
             

                 return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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

         $namesfinds = \DB::table('admissionprocesses')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('admissionprocesses.sadate',[$cstartdate,$cenddate])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
               
              

                 return view('superadmin.student.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      }

   }

  

  
    public function show(students $students)
    {
         $currentMonth = date('m');
         $brnagch = Branch::all();
        $userALl = User::all();
        /* $studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereNull('payments.inviceid')->groupBy('payments.inviceid')->get();*/
          
        $studentsdata = \DB::table('admissionprocesses')->whereMonth('admissionprocesses.sadate',$currentMonth)->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 

        foreach($studentsdata as $studentpaymen)
         {
            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }
        }



         $newStudents = \DB::table('re_admissions')->whereMonth('re_admissions.rsadate',$currentMonth)->select('re_admissions.*','re_admissions.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('re_admissions.id = payments.reinviceid');
                                                            })->get(); 

        foreach($newStudents as $studentpaymen)
         {
            $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }
        }

            $invototal = $studentsdata->sum('invtotal');
        
        $retotal = $newStudents->sum('rinvtotal');

        //dd($invototal);

        $sumtotal = $invototal + $retotal;
          
         $pamenreceived = 0; 
         

         
         $repaymreceived = 0; 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;

            $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();


           return view('superadmin.student.pendingadmission',compact('studentsdata','brnagch','userALl','newStudents','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function create(Branch $branch,course $course,leads $leads)
    {

        $alb = $branch::get();
        $cours = $course::get();
        $leadsdata = leads::get();
         $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        return view('superadmin.student.create',compact('alb','cours','leadsdata','branchdetails','course','taxesna'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $userId = Auth::user()->id;
       /*dd($request->all());*/

        /*$sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";
        $bitolinvno = "0";
        $cvrublinvno = "0";
        $cvrukhinvno = "0";
        $rntuinvno = "0";
        $manipalinvno = "0"*/;

        $mjerno ="0";
        $sjerno ="0";
        $wageron ="0";
        $bitolerno ="0";
        $cvrublerno ="0";
        $cvrukherno ="0";
        $rntuerno ="0";
        $manipalerno ="0";
        
       
        
        $discoun = "NULL";

        $studentnames = $request->studentname;
        $sstudentnames = $request->studentnamesd;
        $birthdate = $request->dob;
        $email = $request->stuemail;
        $brnach = $request->bran;
        $erno = $request->studenterno;
        $mobile = $request->phoneno;
        $stuwhatsapp = $request->whatsno;
        $admidate = $request->adate;
        $studentstreet =  $request->streets;
        $studentcity = $request->city;
        $studentstate = $request->state;
        $studentzipcode =  $request->zipcode;
        $ptime =  $request->preferrabletime;
        $refassignto =  $request->refassignto;
        $refename =  $request->refename;
        $refrom =  $request->refrom;
        $rnote =  $request->remarknote;

      

        $idate = $request->invoicedate;
        $ddate = $request->duedate;
        $branchdata = $request->brnach;
        $invno = $request->invno;
        $pmode = $request->paymentmode;
        $dtype = $request->discounttype;
        $subto = $request->subtotal;
        $tot = $request->total;
        $raxe = $request->taxs;
        $oldpricess = $request->oldtotalpice;
        //$cmode =  $request->coursemode;

        $enrollno = explode("/",$erno);
       

        if($enrollno[0] == 'BITSJ')
        {
            $sjerno = $enrollno[3];

            //dd($enrollno);
        }
        else if($enrollno[0] == 'BITMJ')
        {
            $mjerno = $enrollno[3];
         
        }
        elseif($enrollno[0] == 'BITWG')
        {
            $wageron = $enrollno[3];
        }

         elseif($enrollno[0] == 'BITOL')
        {
            $bitolerno = $enrollno[3];
        }

        elseif($enrollno[0] == 'CVRU(BL)')
        {
            $cvrublerno = $enrollno[3];
        }

        elseif($enrollno[0] == 'CVRU (KH)')
        {
            $cvrukherno = $enrollno[3];
        }

        elseif($enrollno[0] == 'RNTU')
        {
            $rntuerno = $enrollno[3];
        }

        elseif($enrollno[0] == 'MANIPAL')
        {
            $manipalerno = $enrollno[3];
        }

        
       

         if($dtype == "2")
        {
             $discoun = $request->discount1;
        }

        elseif($dtype == "1")
        {
            $discoun = $request->discount2;
        }

        $discoun2 = $request->discount2;

        $inoviceno = explode("/",$invno);

         if($sstudentnames)
        {
            $newstudents = $sstudentnames;
        }

        elseif($studentnames)
        {
            $newstudents = $studentnames;
        }

       


        if($pmode == "EMI") 

        {

            

            $admissionprocessmodel = new admissionprocess();
            $admissionprocess = $admissionprocessmodel->create([
            'studentname'=> $newstudents,
            'sdobs'=> $birthdate,
            'semails'=> $email,
            'sbrnanch'=> $brnach,
            'stobranches'=> $request->tobranchessw,
            'serno'=> $erno,
            'sjerno'=> $sjerno,
            'mjerno'=> $mjerno,
            'wgerno'=> $wageron,
            'bitolerno'=> $bitolerno,
            'cvrublerno'=> $cvrublerno,
            'cvrukherno'=> $cvrukherno,
            'rntuerno'=> $rntuerno,
            'manipalerno'=> $manipalerno,
            'sphone'=> $mobile,
            'swhatsappno'=> $stuwhatsapp,
            'sadate'=> $admidate,
            'sstreet'=> $studentstreet,
            'scity'=> $studentcity,
            'sstate'=> $studentstate,
            'szipcode'=> $studentzipcode,
            'spreferrabbletime'=> $ptime,
            'refeassignto'=> $refassignto,
            'referfrom'=> $refrom,
            'refername'=> $refename,
            'sremarknotes'=> $rnote,
            'ipaymentmodes'=> $pmode,
            'idiscounttypes'=> $dtype,
            'isubtotal'=> $subto,
            'idiscount'=> $discoun,
            'itax'=> $request->tax,
            'invtotal'=> $tot,
            'userid' => $userId,
            'gstprices' => $request->gstprice,
            'oldtotalpice' => $oldpricess,
            'admissionstatus'=> 'New Student',
            'admissionsusersid'=> $request->admissioonsusersid,
            'discounttotal'=> $request->discounttotal,
            'admsisource'=> $request->admisources,
            'fnames'=> $request->fathersnames,
            'mnames'=> $request->mothersname,
            'suniversities'=> $request->universitiesss,
            
            ]);

            $invoicesid = $admissionprocess->id;
                    $coursesdata = $request->invcourse;
                    $courseprice = $request->invprice;
                    $csmode = $request->coursdataemode;
                    $cd = $request->duration;
                    $ct = $request->tax;
                    $installdate = $request->installmentdate;
                    $installprice = $request->installmentprice;
                    $pamount = $request->pendingamount;
                     $adforss= $request->admissionfor;

                    $adforss= $request->admissionfor;
                    $uniccourse= $request->unvicocurs;
                    $ufees= $request->univfees;
                    $subcoursesdata = $request->invsubcourses;

                    if($admissionprocess->suniversities == 'BIT')
                    {
                           for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'courseid'   => $coursesdata[$i],
                                    'subcourses'   => $subcoursesdata[$i],
                                    'coursemode'   => $csmode[$i],
                                    'courseprice'   => $courseprice[$i],
                                    'studentsin'   => 'New Student',
                                    
                                ]);
                                $admissionprocesscourses->save();
                        }
                    }

                    else

                    {
                          for($i=0; $i < (count($uniccourse)); $i++)
                        {
                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'univecoursid'   => $uniccourse[$i],
                                    'admissionfor'   => $adforss[$i],
                                    'unoverfeess'   => $ufees[$i],
                                    'studentsin'   => 'New Student',
                                    
                                ]);
                                $admissionprocesscourses->save();
                        }
                    }
                    for($k=0; $k <(count($installdate)); $k++)
                    {
                        $admissionprocessinstallmentfees = new admissionprocessinstallmentfees([
                            
                            'invoid' => $invoicesid,
                            'invoicedate'   => $installdate[$k],
                            'installmentamount'   => $installprice[$k],
                            'pendinamount'   => $pamount[$k],

                        ]);

                         $admissionprocessinstallmentfees->save();  
                    }


             return redirect('/view-invoice/'.$invoicesid);

        }

        else
        {
            $admissionprocessmodel = new admissionprocess();
            $admissionprocess = $admissionprocessmodel->create([
            'studentname'=> $newstudents,
            'sdobs'=> $birthdate,
            'semails'=> $email,
            'sbrnanch'=> $brnach,
            'stobranches'=> $request->tobranchessw,
            'serno'=> $erno,
            'sjerno'=> $sjerno,
            'mjerno'=> $mjerno,
            'wgerno'=> $wageron,
            'bitolerno'=> $bitolerno,
            'cvrublerno'=> $cvrublerno,
            'cvrukherno'=> $cvrukherno,
            'rntuerno'=> $rntuerno,
            'manipalerno'=> $manipalerno,
            'sphone'=> $mobile,
            'swhatsappno'=> $stuwhatsapp,
            'sadate'=> $admidate,
            'sstreet'=> $studentstreet,
            'scity'=> $studentcity,
            'sstate'=> $studentstate,
            'szipcode'=> $studentzipcode,
            'spreferrabbletime'=> $ptime,
            'refeassignto'=> $refassignto,
            'referfrom'=> $refrom,
            'refername'=> $refename,
            'sremarknotes'=> $rnote,
            'invdate'=> $idate,
            'duedate'=> $ddate,
            'ipaymentmodes'=> $pmode,
            'idiscounttypes'=> $dtype,
            'isubtotal'=> $subto,
            'idiscount'=> $discoun,
            'itax'=> $request->tax,
            'invtotal'=> $tot,
            'userid' => $userId,
            'gstprices' => $request->gstprice,
            'oldtotalpice' => $request->oldtotalpice,
            'admissionstatus'=> 'New Student',
            'admissionsusersid'=> $request->admissioonsusersid,
            'discounttotal'=> $request->discounttotal,
            'admsisource'=> $request->admisources,
            'fnames'=> $request->fathersnames,
            'mnames'=> $request->mothersname,
            'suniversities'=> $request->universitiesss,
            
            ]);

            $invoicesid = $admissionprocess->id;
                    $coursesdata = $request->invcourse;
                    $subcoursesdata = $request->invsubcourses;
                    $courseprice = $request->invprice;
                    $csmode = $request->coursdataemode;
                    $cd = $request->duration;
                    $ct = $request->tax;
                    $installdate = $request->installmentdate;
                    $installprice = $request->installmentprice;
                    $pamount = $request->pendingamount;
                    

                    $adforss= $request->admissionfor;
                    $uniccourse= $request->unvicocurs;
                    $ufees= $request->univfees;
                    $subcoursesdata = $request->invsubcourses;

                    if($admissionprocess->suniversities == 'BIT')
                    {
                            for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'courseid'   => $coursesdata[$i],
                                    'subcourses'   => $subcoursesdata[$i],
                                    'coursemode'   => $csmode[$i],
                                    'courseprice'   => $courseprice[$i],
                                    'studentsin'   => 'New Student',
                                    
                                ]);
                                $admissionprocesscourses->save();
                        }
                    }

                    else

                    {
                          for($i=0; $i < (count($uniccourse)); $i++)
                        {
                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'univecoursid'   => $uniccourse[$i],
                                    'admissionfor'   => $adforss[$i],
                                    'unoverfeess'   => $ufees[$i],
                                    'studentsin'   => 'New Student',
                                    
                                ]);
                                $admissionprocesscourses->save();
                        }
                    }

                 

             return redirect('/create-payment/'.$invoicesid);
                    

        }
    }

     public function update($id,Request $request, students $students)
    { 

       if($request->discounttype == "1")
        {
             $discoun = $request->discount1;
        }

        elseif($$request->discounttype == "2")
        {
            $discoun = $request->discount2;
        }
         
         if($request->paymentmode == "EMI")
         {

                                 if($request->universitiesss == 'BIT')
                                 {
                                    $dele = admissionprocesscourses::where('invid',$id)->get();
                                    $dele->each->delete();
                                 }

                                 else
                                 {
                                     $deles = admissionprocesscourses::where('invid',$id)->get();
                                     $deles->each->delete();
                                 }
                                
                                $deleted = admissionprocessinstallmentfees::where('invoid',$id)->get();
                                $deleted->each->delete();



              $updates = admissionprocess::find($id);
              $updates->studentname = $request->studentname;
              $updates->fnames = $request->fathersnames;
              $updates->mnames = $request->mothersname;
              $updates->sdobs = $request->dob;
              $updates->semails = $request->stuemail;
              $updates->sphone = $request->phoneno;
              $updates->swhatsappno = $request->whatsno;
              $updates->sadate = $request->adate;
              $updates->sbrnanch = $request->bran;
              $updates->stobranches = $request->tobranchessw;
              $updates->suniversities = $request->universitiesss;
              $updates->sstreet = $request->streets;
              $updates->scity = $request->city;
              $updates->sstate = $request->state;
              $updates->szipcode = $request->zipcode;
              $updates->spreferrabbletime = $request->preferrabletime;
              $updates->refeassignto = $request->assignto;
              $updates->referfrom = $request->refename;
              $updates->refername = $request->refrom;
              $updates->sremarknotes = $request->remarknote;
              $updates->Ibranchs = $request->brnach;
              $updates->Invoiceno = $request->invno;
              $updates->invdate = $request->invoicedate;
              $updates->duedate = $request->duedate;
              $updates->ipaymentmodes = $request->paymentmode;
              $updates->idiscounttypes = $request->discounttype;
              $updates->isubtotal = $request->subtotal;
              $updates->discounttotal = $request->discounttotal;
              $updates->idiscount = $discoun;
              $updates->itax = $request->tax;
              $updates->gstprices = $request->gstprice;
              $updates->invtotal = $request->total;
              $updates->save();



              if($request->universitiesss == 'BIT')
                                 {

                                    $maincourse = $request->invcourse;
                                    $cmodes = $request->coursdataemode;
                                    $inmvsprice = $request->invprice;


                                   

                                     for($i=0; $i < (count($maincourse)); $i++)
                                        {
                                            
                                             $dakmsm = admissionprocesscourses::updateOrCreate(['courseid' => $maincourse[$i],'coursemode' => $cmodes[$i],'courseprice' => $inmvsprice[$i],'invid' => $id ]);

                                          
                  


                                        }
                                 }

                                 else
                                 {
                                    $univcourse = $request->unvicocurs;
                                    $admissfor = $request->admissionfor;
                                    $ufees = $request->univfees;

                                      for($i=0; $i < (count($univcourse)); $i++)
                                        {
                                            
                                             $dakmsm = admissionprocesscourses::updateOrCreate(['univecoursid' => $univcourse[$i],'admissionfor' => $admissfor[$i],'unoverfeess' => $ufees[$i],'invid' => $id ]);

                                          
                  


                                        }
                                 }

                                 $idates = $request->installmentdate;
                                 $iprice = $request->installmentprice;
                                 $pprice = $request->pendingamount;
                                  for($j=0; $j < (count($idates)); $j++)
                                        {
                                            
                                             $dakmsm = admissionprocessinstallmentfees::updateOrCreate(['invoicedate' => $subcourse[$j],'installmentamount' => $iprice[$j],'pendinamount' => $pprice[$j],'invoid' => $id ]);

                                          
                  


                                        }


                              return redirect('/student')->with('success','Admission Updated successfully!');



         }


         else
         {
                        if($request->universitiesss == 'BIT')
                                 {
                                    $dele = admissionprocesscourses::where('invid',$id)->get();
                                    $dele->each->delete();
                                 }

                                 else
                                 {
                                     $deles = admissionprocesscourses::where('invid',$id)->get();
                                     $deles->each->delete();
                                 }
                                
                                $deleted = admissionprocessinstallmentfees::where('invoid',$id)->get();
                                $deleted->each->delete();



              $updates = admissionprocess::find($id);
              $updates->studentname = $request->studentname;
              $updates->fnames = $request->fathersnames;
              $updates->mnames = $request->mothersname;
              $updates->sdobs = $request->dob;
              $updates->semails = $request->stuemail;
              $updates->sphone = $request->phoneno;
              $updates->swhatsappno = $request->whatsno;
              $updates->sadate = $request->adate;
              $updates->sbrnanch = $request->bran;
              $updates->stobranches = $request->tobranchessw;
              $updates->suniversities = $request->universitiesss;
              $updates->sstreet = $request->streets;
              $updates->scity = $request->city;
              $updates->sstate = $request->state;
              $updates->szipcode = $request->zipcode;
              $updates->spreferrabbletime = $request->preferrabletime;
              $updates->refeassignto = $request->assignto;
              $updates->referfrom = $request->refename;
              $updates->refername = $request->refrom;
              $updates->sremarknotes = $request->remarknote;
              $updates->Ibranchs = $request->brnach;
              $updates->Invoiceno = $request->invno;
              $updates->invdate = $request->invoicedate;
              $updates->duedate = $request->duedate;
              $updates->ipaymentmodes = $request->paymentmode;
              $updates->idiscounttypes = $request->discounttype;
              $updates->isubtotal = $request->subtotal;
              $updates->discounttotal = $request->discounttotal;
              $updates->idiscount = $discoun;
              $updates->itax = $request->tax;
              $updates->gstprices = $request->gstprice;
              $updates->invtotal = $request->total;
              $updates->save();



              if($request->universitiesss == 'BIT')
                                 {

                                    $maincourse = $request->invcourse;
                                    $cmodes = $request->coursdataemode;
                                    $inmvsprice = $request->invprice;


                                   

                                     for($i=0; $i < (count($maincourse)); $i++)
                                        {
                                            
                                             $dakmsm = admissionprocesscourses::updateOrCreate(['courseid' => $maincourse[$i],'coursemode' => $cmodes[$i],'courseprice' => $inmvsprice[$i],'invid' => $id ]);

                                          
                  


                                        }
                                 }

                                 else
                                 {
                                    $univcourse = $request->unvicocurs;
                                    $admissfor = $request->admissionfor;
                                    $ufees = $request->univfees;

                                      for($i=0; $i < (count($univcourse)); $i++)
                                        {
                                            
                                             $dakmsm = admissionprocesscourses::updateOrCreate(['univecoursid' => $univcourse[$i],'admissionfor' => $admissfor[$i],'unoverfeess' => $ufees[$i],'invid' => $id ]);

                                          
                  


                                        }
                                 }


                              return redirect('/student')->with('success','Admission Updated successfully!');
         }

      


    } 

    public function leadsdata($studentdata,leads $leads)
    {
        //$data = "data listed";

        $data = leads::where('studentname', '=', $studentdata)->get();
        
        return  response()->json($data);

    }


    public function mutlipleajaxprice($courseId,course $course)
    {
            $courseprice = course::where('id', '=', $courseId)->get();

        
            return response()->json($courseprice);
    }

     public function ajaxprice($dataid,course $course)
    {
            $dataprice = course::where('id', '=', $dataid)->get();

        
            return response()->json($dataprice);
    }

    public function ajax($branchdata,students $students)
    {
         $year = date("Y");
         $month = date("m");

         if($branchdata == "BITSJ")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = admissionprocess::where('stobranches','=',$branchdata)->latest()->get()->pluck('sjerno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITSJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }

        else if($branchdata == "BITMJ") 
        {

            
            $latests = admissionprocess::where('stobranches','=',$branchdata)->latest()->get()->pluck('mjerno');
            //$latests = admissionprocess::get()->pluck('mjerno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITMJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

          else if($branchdata == "BITWG") 
        {

            
            $latests = admissionprocess::where('sbrnanch','=',$branchdata)->latest()->get()->pluck('wgerno');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITWG/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

         else if($branchdata == "BITOL") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$branchdata)->latest()->get()->pluck('bitolerno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'BITOL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($branchdata == "CVRU(BL)") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$branchdata)->latest()->get()->pluck('cvrublerno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(BL)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($branchdata == "CVRU (KH)") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$branchdata)->latest()->get()->pluck('cvrukherno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(KH)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($branchdata == "RNTU") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$branchdata)->latest()->get()->pluck('rntuerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'RNTU/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if($branchdata == "MANIPAL") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$branchdata)->latest()->get()->pluck('manipalerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'MANIPAL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\students  $students
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\students  $students
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $alb = branch::get();
        $cours = course::get();
        $leadsdata = leads::get();
         $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        $directstudentsdata = admissionprocess::find($id);
        $ad = admissionprocess::all();
        $adcourses = admissionprocesscourses::where('invid','=',$id)->get();
        $univcourses = admissionprocesscourses::where('invid','=',$id)->get();
        $ademi = admissionprocessinstallmentfees::where('invoid','=',$id)->get();
        $ucats = UnviersitiesCategory::all();
        
       
        
        return view('superadmin.student.edit',compact('alb','cours','leadsdata','branchdetails','course','taxesna','directstudentsdata','ad','adcourses','ademi','ucats'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\students  $students
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\students  $students
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,students $students)
    {
        //

        $deletss = students::find($id);
        $deletss->delete();

          return redirect('/student')->with('success','Student Deleted Successfully');
    }

    public function direct(leads $leads,Branch $branch,course $course,Request $request)
    {
         $id = $request->getadmissions;
         //dd($id);

        $alb = branch::get();
        $directstudentsdata = leads::find($id);
        $cours = course::get();
        $leadsdata = leads::get();

        $studentdetails = students::get();
       
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        $ucats = UnviersitiesCategory::all();


        return view('superadmin.student.direct',compact('alb','cours','leadsdata','directstudentsdata','studentdetails','branchdetails','course','taxesna','ucats'));
    }

    public function directbranchdata($directbranchdata,students $students)
    {
        $year = date("Y");
         $month = date("m");

               if($directbranchdata == "BITSJ")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('sjerno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITSJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }

        else if($directbranchdata == "BITMJ") 
        {

            
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('mjerno');
            //$latests = admissionprocess::get()->pluck('mjerno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITMJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

          else if($directbranchdata == "BITWG") 
        {

            
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('wgerno');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITWG/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

         else if($directbranchdata == "BITOL") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('bitolerno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'BITOL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($directbranchdata == "CVRU(BL)") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('cvrublerno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(BL)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($directbranchdata == "CVRU (KH)") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('cvrukherno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(KH)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($directbranchdata == "RNTU") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('rntuerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'RNTU/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if($directbranchdata == "MANIPAL") 
        {
           
            $latests = admissionprocess::where('stobranches','=',$directbranchdata)->latest()->get()->pluck('manipalerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'MANIPAL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
    }

    public function coursronlineofflineprice($courseId,$cours,course $course)
    {

        if($courseId == 'Online Mode')
        {   

            $courseprice = course::where('id',$cours)->pluck('courseonlineprice');
            return response()->json($courseprice);
        }

        elseif($courseId == 'Offline Mode')
        {
            $courseprice = course::where('id',$cours)->pluck('courseprice');
            return response()->json($courseprice);
        }

    }


    public function couurseofflineonlinemodeprice($cours,$coursmode,course $course)
    {
        if($coursmode == 'Online Mode')
        {   

            $selectedprice = course::where('id',$cours)->pluck('courseonlineprice');
            return response()->json($selectedprice);
        }

        elseif($coursmode == 'Offline Mode')
        {
            $selectedprice = course::where('id',$cours)->pluck('courseprice');
            return response()->json($selectedprice);
        }
    }


    public function admissionsubcourse($maincourseId)
    {
        $subcourse = coursespecializationlist::where('coursessid',$maincourseId)->get();
        
        return response()->json($subcourse);
    }

    public function specializationcourse($SpeicializationCourse)
    {
        $speciaizationcoiurse = coursespecializationlist::where('coursessid',$SpeicializationCourse)->get();
         return response()->json($speciaizationcoiurse);
    }


    public function universitiesfrwssss(course $course,$universititescoursse,$universitiescoursbrnaches)
    {   

       
           // dd($universititescoursse,$universitiescoursbrnaches);
           $selectedprice = universititiesfeeslist::where('coursid',$universitiescoursbrnaches)->where('universitiesfor',$universititescoursse)->pluck('overallfees');
           /* $selectedprice = DB::select("SELECT overallfees FROM universititiesfeeslists WHERE coursid = '$universitiescoursbrnaches' AND universitiesfor = '$universititescoursse'");*/



            return response()->json($selectedprice);
    }

    public function studentadmissionpro($getmobileno)
    {
        /*$getadmission = admissionprocess::where('sphone',$getmobileno)->get();

        return response()->json($getadmission);*/

        if($getadmission = admissionprocess::where('sphone',$getmobileno)->pluck('stobranches'))
            {
                    $msg = "Admission Already Availble In Branch $getadmission";
                    return response()->json($msg);
            }

            else
            {
                $msg = " ";
                 return response()->json($msg);
            }
    }

    public function getcourseslist(Request $request)
    {
        $admissionsId = $request->admissionid;

        $data= array();

        $aprod = admissionprocess::find($admissionsId);

        $abranch = $aprod->suniversities;
        //dd($abranch);

        if($abranch == 'BIT')
        {
             $result = admissionprocesscourses::select('courses.coursename','admissionprocesscourses.*')->leftjoin('courses','courses.id','=','admissionprocesscourses.courseid')->where('admissionprocesscourses.invid',$admissionsId)->get();

              foreach($result as $res)
              {
                  $row = array();
                  $row[] = $res->coursename;
                  $row[] = $res->coursemode;
                  $data[] = $row;
              }

               $response = array(
                  "recordsTotal"    => count($data),  
                  "recordsFiltered" => count($data), 
                  "data"            => $data   
               );

               echo json_encode($response);
        }

        else
        {

             $result = admissionprocesscourses::select('courses.coursename','admissionprocesscourses.*')->leftjoin('courses','courses.id','=','admissionprocesscourses.univecoursid')->where('admissionprocesscourses.invid',$admissionsId)->get();

              foreach($result as $res)
              {
                  $row = array();
                  $row[] = $res->coursename;
                  $row[] = $res->admissionfor;
                  $data[] = $row;
              }

               $response = array(
                  "recordsTotal"    => count($data),  
                  "recordsFiltered" => count($data), 
                  "data"            => $data   
               );

            echo json_encode($response);

        }

       // dd($abranch);


       

    }

    public function getstudertnsname($k)
    {
         $getresult = admissionprocess::find($k);

         return response()->json($getresult->studentname);

    }

    public function allreceipts(Request $request)
    {
        $admissionsId = $request->admissionid;

        $data= array();

        $result = payment::where('inviceid',$admissionsId)->get();

        foreach($result as $res)
        {
            $row = array();
            $row[] = '<a href="/paymentreceipt/'.$res->id.'" class="btn btn-primary"><i class="fas fa-file-invoice"></i></a>';
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

    }

    public function paymentreceiptlists($id)
    {
      $result = payment::where('inviceid',$id)->orderBy('id','DESC')->get();

      $admissionname = admissionprocess::find($id);

      return view('superadmin.student.paymentreceiptlist',compact('result','admissionname'));

    }

    public function deletedata($id)
    {                   
                                    $delse = admissionprocess::find($id);
                                    $delse->delete();
     
                                    $dele = admissionprocesscourses::where('invid',$id)->get();
                                    $dele->each->delete();
                                    
                                    $deles = admissionprocesscourses::where('invid',$id)->get();
                                    $deles->each->delete();
                               
                                
                                $deleted = admissionprocessinstallmentfees::where('invoid',$id)->get();
                                $deleted->each->delete();

                                return redirect()->back()->with('success','Admission Deleted successfully!');
    }
}
