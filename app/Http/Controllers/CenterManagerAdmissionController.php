<?php

namespace App\Http\Controllers;
use App\students;
use App\course;
use App\Branch;
use App\leads;
use App\payment;
use App\invoices;
use App\studentscourse;
use App\Tax;
use App\User;
use App\ReAdmission;
use App\followup;
use App\PaymentSource;
use App\coursecategory;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\Source;
use App\admissionprocessinstallmentfees;
use App\UnviersitiesCategory;
use Auth;
use DB;
use Illuminate\Http\Request;

class CenterManagerAdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function index()
    {
          $userId = Auth::user()->id;
        
        //$cour = course::all();
       //$sourcedata = Source::get();
        //$folss = followup::get();
         //$userBranch = Auth::user()->branchs;
         //$userdata = User::where('branchs',$userBranch)->get();


         $userBranch = Auth::user()->branchs;
        $currentMonth = date('m');

      

        $invoicesdata = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

        $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereMonth('payments.paymentdate',$currentMonth)->where('re_admissions.rstobranches',$userBranch)->orderBy('payments.id','DESC')->get();
         

                  $cour = course::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                 $userdata = User::where('branchs',$userBranch)->get();
              $sourcedata = Source::get();
              $ccatall = coursecategory::get();
              $folss = followup::get();
          

                return view('centremanager.invoice.invoicesdata',compact('invoicesdata','reinvoicesdata','cour','sourcedata','folss','branchdata','ccatall','userdata'));
    }


  public function filterfees(Request $request)
   {

        $userBranch = Auth::user()->branchs;
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
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

   
           $namesfinds = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->get();

       // $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereMonth('payments.paymentdate',$currentMonth)->where('re_admissions.rstobranches',$userBranch)->orderBy('payments.id','DESC')->get();

            $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->Where('re_admissions.rstudents', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->get();
        

          return view('centremanager.invoice.filterfees',compact('namesfinds','reinvoicesdata','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

         $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orderBy('payments.id','DESC')->get();

          $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->where('re_admissions.rsphone',$mobdatas)->orWhere('re_admissions.rswhatsappno',$mobdatas)->orderBy('payments.id','DESC')->get();

       

          return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

               

                $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();
               

                return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();
               
               

                return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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
             $susfindcourse = course::where('id',$coursedatas)->pluck('byuniversitites');


             if($susfindcourse = 'BIT Institute')
               {
                
                  $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->where('readmissioncourses.recourseid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();
               

                return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));

               }


               else
               {
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->where('readmissioncourses.reunivecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();
               

                return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
               }


      }

     
      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();


           $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.id','DESC')->get();



         $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->where('re_admissions.radmsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.id','DESC')->get();
          
         

          return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }

      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

  

           $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->orderBy('payments.id','DESC')->get();
               
             $reinvoicesdata = "";
               
             

                return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        
          $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->where('admissionprocesses.stobranches',$bransdata)->orderBy('payments.id','DESC')->get();
         
         
            $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->where('re_admissions.rstobranches', $bransdata)->orderBy('payments.id','DESC')->get();

                return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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
                  $susfindcourse = course::where('cat_id',$categorydata)->pluck('byuniversitites');

                  if($susfindcourse = 'BIT Institute')
                  {
                     //dd('test');

                     
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->whereIn('admissionprocesscourses.courseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->whereIn('readmissioncourses.recourseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get(); 
                     
                    

                      return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }


                  else
                  {
                    

                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->whereIn('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->whereIn('readmissioncourses.reunivecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get(); 
                     
                    

                      return view('centremanager.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }
           
          }  
   }



     public function pendingfees()
     {

       
      
        $currentMonth = date('m');
          $userBranch = Auth::user()->branchs;


        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid','payments.id as pids')
         ->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.stobranches',$userBranch)
         ->groupBy('payments.inviceid')
         ->orderBy('payments.id','DESC')
         ->get();


 

        $ReWiPayment = ReAdmission::select('re_admissions.*','payments.id as pids','payments.*','re_admissions.id as reid')
         ->join('payments', 'payments.reinviceid', '=', 're_admissions.id')
         ->orderBy('payments.id','DESC')
         ->where('re_admissions.rstobranches',$userBranch)
         ->groupBy('payments.reinviceid')
         ->get();
        
        $pendamount = $WiPayment;
        $rependamount = $ReWiPayment;

        $invototal = $pendamount->sum('invtotal');
        
        $retotal = $rependamount->sum('rinvtotal');



        $sumtotal = $invototal + $retotal;
          
         $pamenreceived = $pendamount->sum('paymentreceived');
         

         
         $repaymreceived = $rependamount->sum('paymentreceived'); 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;

              $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();



      //  dd($pendamount);
        return view('centremanager.invoice.pendingfees',compact('pendamount','rependamount','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));

    }

    public function changeemi($id)
    {

       $adm = admissionprocess::find($id); 
    $latestpaymentdata = payment::where('inviceid',$id)->orderBy('id','DESC')->first();
    $getemidata = payment::where('inviceid',$id)->get();
    foreach($getemidata as $emis)
       {
           $getinstallmentdata = admissionprocessinstallmentfees::where('id',$emis->installmentid)->get();
       }

     /*  $getinstallmentdata = admissionprocessinstallmentfees::select('admissionprocessinstallmentfees.*')->join('payments','payments.installmentid','=','admissionprocessinstallmentfees.id')->whereExist('admissionprocessinstallmentfees.id',$id)->get();*/

       //dd($getinstallmentdata);


     /*   dd($getinstallmentdata);*/

        return view('centremanager.invoice.changeemi',compact('getinstallmentdata','adm','latestpaymentdata'));
    }
     public function changenewemi($id,Request $request)
    {                        /* dd($request->all());*/      
                                $bids = $request->emimainid;
                            //        dd($bids);
                                $deles = admissionprocessinstallmentfees::where('invoid',$bids)->get();
                                $deles->each->delete();
                                  
                               


                                $idate = $request->installmentdate;
                                $iprice = $request->installmentprice;
                                $ipa = $request->pendingamount;


                                for($i=0; $i < (count($idate)); $i++)
                                        {
                                            
                                             $dakmsm = admissionprocessinstallmentfees::updateOrCreate(['invoicedate' => $idate[$i],'invoid' => $id,'installmentamount' => $iprice[$i],'pendinamount' => $ipa[$i] ]);

                                          
                  


                                        }

                                    DB::statement('update admissionprocessinstallmentfees a inner join payments c on a.invoid = c.inviceid and  a.installmentamount = c.totalamount set a.status = 1, c.installmentid = a.id;');

   
            return redirect('/centre-manager-re-payment/'.$id)->with('success','EMI Successfully Changed !!');

    }




    
     public function filterpendingfees(Request $request)
     {
        $userBranch = Auth::user()->branchs;

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
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

          $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
           ->Where('admissionprocesses.stobranches',$userBranch)
         ->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
         ->groupBy('payments.inviceid')
         ->get();
        

          return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
         $namesfinds =  admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->where('admissionprocesses.stobranches',$userBranch)
        ->Where('admissionprocesses.sphone', $mobdatas)
        ->orwhere('admissionprocesses.swhatsappno',$mobdatas)
         ->groupBy('payments.inviceid')
         ->get();
       

          return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

               

               $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
           ->where('admissionprocesses.stobranches',$userBranch)
       ->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])
         ->groupBy('payments.inviceid')
         ->get();
               

                return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
           ->where('admissionprocesses.stobranches',$userBranch)
       ->whereBetween('payments.paymentdate',[$startdates,$enddats])
         ->groupBy('payments.inviceid')
         ->get(); 
               

                return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();

         $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')  ->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
         ->groupBy('payments.inviceid')
         ->get(); 
         

          return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }

     

      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        
        $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.admsisource',$sources)
           ->where('admissionprocesses.stobranches',$userBranch)
         ->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
         ->groupBy('payments.inviceid')
         ->get(); 
         

          return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }



     


      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

  

          

          $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.admissionsusersid',$asearch)
           ->where('admissionprocesses.stobranches',$userBranch)
         ->whereBetween('payments.paymentdate',[$asdates,$aenddates])
         ->groupBy('payments.inviceid')
         ->get(); 
               
             

                return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

          

          $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
         ->groupBy('payments.inviceid')
         ->get();
               
             

                return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
      }


      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
          

         $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
         ->where('admissionprocesses.stobranches',$userBranch)
         ->where('admissionprocesscourses.courseid',$findcourse)
         ->orWhere('admissionprocesscourses.univecoursid',$findcourse)
         ->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
         ->groupBy('payments.inviceid')
         ->get(); 
               
              

                return view('centremanager.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      }  

  }


    public function totalinvociess()
    {
        $UserBranch = Auth::user()->branchs;
        $currentMonth = date('m');
          $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->where('admissionprocesses.stobranches',$UserBranch)
          ->whereMonth('payments.paymentdate',$currentMonth)
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid')
                                                                 ->orderBy('payments.paymentdate','DESC');
                        
                                                            })->get(); 


        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.stobranches',$UserBranch)
         ->whereMonth('payments.paymentdate',$currentMonth)
         ->orderBy('payments.paymentdate','DESC')
         ->groupBy('payments.inviceid')
         ->get();
        
        $pendamount = $NewPayment->merge($WiPayment);

         $ReNewPayment = \DB::table('re_admissions')
          ->leftJoin('payments', 'payments.reinviceid', '=', 're_admissions.id')
          ->where('re_admissions.rstobranches',$UserBranch)
          ->whereMonth('re_admissions.rsadate',$currentMonth)
          ->select('re_admissions.*','payments.*','re_admissions.id as remid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('re_admissions.id = payments.reinviceid')
                                                                 ->groupBy('payments.reinviceid');
        
                                                            })->orderBy('re_admissions.id','DESC')->get(); 


        $ReWiPayment = ReAdmission::select('re_admissions.*', 'payments.*','re_admissions.id as remid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.reinviceid', '=', 're_admissions.id')
          ->where('re_admissions.rstobranches',$UserBranch)
         ->whereMonth('payments.paymentdate',$currentMonth)
         ->groupBy('payments.reinviceid')
         ->orderBy('re_admissions.id','DESC')
         ->get();

         $rependamount = $ReNewPayment->merge($ReWiPayment);

        $cour = course::all();
                 $branchdata = Branch::where('branchname',$UserBranch)->get();
                 $userdata = User::where('branchs',$UserBranch)->get();
              $sourcedata = Source::get();
              $ccatall = coursecategory::get();
              $folss = followup::get();

         $invototal = $pendamount->sum('invtotal');
        
        $retotal = $rependamount->sum('rinvtotal');


         $sumtotal = $invototal + $retotal;
          
         $pamenreceived = $pendamount->sum('paymentreceived'); 
         

         
         $repaymreceived = 0; 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;

        return view('centremanager.invoice.totalinvocies',compact('pendamount','cour','branchdata','userdata','sourcedata','ccatall','folss','rependamount','sumtotal','totslreceived','remainingamount'));
    }

     public function filtertotalinvoices(Request $request)
     {

        $userBranch = Auth::user()->branchs;
        
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
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

          //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

             


              $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$userBranch)->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
               ->where('admissionprocesses.stobranches',$userBranch)
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment);
           

             return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
         }

         elseif($mobdatas = $request->getMobilesno)
         {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
            //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orderBy('payments.paymentdate','DESC')->get();

             $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)
          ->where('admissionprocesses.stobranches',$userBranch)
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)
               ->where('admissionprocesses.stobranches',$userBranch)
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment); 

          

             return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
         }


         elseif($datesfor = $request->DateFor)
         {  
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Admission Date")
            {


               $folss = followup::get();
               $userdata = User::where('branchs',$userBranch)->get();
                  $cour = course::all();
                     $sourcedata = Source::all();
                     $branchdata = Branch::where('branchname',$userBranch)->get();
                     $ccatall = coursecategory::get();

                  

                 // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                     $NewPayment = \DB::table('admissionprocesses')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])
                                                           ->whereNotExists( function ($query) {
                                                           $query->select(DB::raw(1))
                                                                   ->from('payments')
                                                                   ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                    ->groupBy('payments.inviceid');
           
                                                               })->orderBy('payments.paymentdate','DESC')->get(); 


                 $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                  ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                  ->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])
                  ->groupBy('payments.inviceid')
                  ->orderBy('payments.paymentdate','DESC')
                  ->get();

                 $namesfinds = $NewPayment->merge($WiPayment); 
                  

                   return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
               }

                elseif($datesfor == "Payment Date")
               {


                  $folss = followup::get();
                  $userdata = User::where('branchs',$userBranch)->get();
                     $cour = course::all();
                     $sourcedata = Source::all();
                     $branchdata = Branch::where('branchname',$userBranch)->get();
                     $ccatall = coursecategory::get();

                    //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                       $NewPayment = \DB::table('admissionprocesses')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])
                                                           ->whereNotExists( function ($query) {
                                                           $query->select(DB::raw(1))
                                                                   ->from('payments')
                                                                   ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                    ->groupBy('payments.inviceid');
           
                                                               })->orderBy('payments.paymentdate','DESC')->get(); 


                 $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                  ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                  ->where('admissionprocesses.stobranches',$userBranch)
                  ->whereBetween('payments.paymentdate',[$startdates,$enddats])
                  ->groupBy('payments.inviceid')
                  ->orderBy('payments.paymentdate','DESC')
                  ->get();

                 $namesfinds = $NewPayment->merge($WiPayment); 
                     

                      return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                  }

               

              

               
            }

            elseif($coursedatas = $request->coursedatas)
            {
               $folss = followup::get();
               $userdata = User::where('branchs',$userBranch)->get();
                  $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();
                  $cstartsdates = $request->cdatestat;
                  $cendsdates = $request->cdateend;
               //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();

              // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get();

                 $NewPayment = \DB::table('admissionprocesses')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
             ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
                                                           ->whereNotExists( function ($query) {
                                                           $query->select(DB::raw(1))
                                                                   ->from('payments')
                                                                   ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                    ->groupBy('payments.inviceid');
           
                                                               })->orderBy('payments.paymentdate','DESC')->get(); 


                 $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                  ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                  ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
                  ->where('admissionprocesses.stobranches',$userBranch)
                  ->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
                  ->groupBy('payments.inviceid')
                  ->orderBy('payments.paymentdate','DESC')
                  ->get();

                 $namesfinds = $NewPayment->merge($WiPayment);  
               

                return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
            }




            elseif($sources = $request->sourceSearch)
            {
               $starsdates = $request->sdatestat;
               $enssdates = $request->sdateend;

               $folss = followup::get();
               $userdata = User::where('branchs',$userBranch)->get();
                  $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

              

                //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.paymentdate','DESC')->get(); 

                   $NewPayment = \DB::table('admissionprocesses')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
                                                           ->whereNotExists( function ($query) {
                                                           $query->select(DB::raw(1))
                                                                   ->from('payments')
                                                                   ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                    ->groupBy('payments.inviceid');
           
                                                               })->orderBy('payments.paymentdate','DESC')->get(); 


                 $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                  ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                  ->where('admissionprocesses.stobranches',$userBranch)
                  ->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
                  ->groupBy('payments.inviceid')
                  ->orderBy('payments.paymentdate','DESC')
                  ->get();

                 $namesfinds = $NewPayment->merge($WiPayment);
               

                return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
            }



         


            elseif($asearch = $request->AssignedToSearch)
            {
               $asdates = $request->AstartDate;
               $aenddates = $request->AEndDate;

               $folss = followup::get();
               $userdata = User::where('branchs',$userBranch)->get();
                  $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

        

                  // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
                       $NewPayment = \DB::table('admissionprocesses')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])
                                                           ->whereNotExists( function ($query) {
                                                           $query->select(DB::raw(1))
                                                                   ->from('payments')
                                                                   ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                    ->groupBy('payments.inviceid');
           
                                                               })->orderBy('payments.paymentdate','DESC')->get(); 


                 $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                  ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                  ->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])
                  ->groupBy('payments.inviceid')
                  ->orderBy('payments.paymentdate','DESC')
                  ->get();

                 $namesfinds = $NewPayment->merge($WiPayment);
                   

                      return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
            }


            elseif($bransdata = $request->branchSearchDatas)
            {
               $bstartdate = $request->BStartDate;
               $benddate = $request->BEnddate;

               $folss = followup::get();
               $userdata = User::where('branchs',$userBranch)->get();
                  $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

              // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

               // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->orderBy('payments.paymentdate','DESC')->get();

                $NewPayment = \DB::table('admissionprocesses')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
                                                           ->whereNotExists( function ($query) {
                                                           $query->select(DB::raw(1))
                                                                   ->from('payments')
                                                                   ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                    ->groupBy('payments.inviceid');
           
                                                               })->orderBy('payments.paymentdate','DESC')->get(); 


                 $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                  ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                  ->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
                  ->groupBy('payments.inviceid')
                  ->orderBy('payments.paymentdate','DESC')
                  ->get();

                 $namesfinds = $NewPayment->merge($WiPayment); 
                     
                   

                      return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
            }


            elseif($categorydata = $request->categorysDatas)
            {

               //dd($categorydata);
               $cstartdate = $request->CStartDate;
               $cenddate = $request->CEnddate;

               $folss = followup::get();
               $userdata = User::where('branchs',$userBranch)->get();
                  $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

                  $findcourse = course::where('cat_id',$categorydata)->pluck('id');
                 
                

                    $NewPayment = \DB::table('admissionprocesses')
             ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
             ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
             ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
                                                           ->whereNotExists( function ($query) {
                                                           $query->select(DB::raw(1))
                                                                   ->from('payments')
                                                                   ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                    ->groupBy('payments.inviceid');
           
                                                               })->orderBy('payments.paymentdate','DESC')->get(); 


                 $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                  ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                  ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
                  ->where('admissionprocesses.stobranches',$userBranch)
                  ->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
                  ->groupBy('payments.inviceid')
                  ->orderBy('payments.paymentdate','DESC')
                  ->get();

                 $namesfinds = $NewPayment->merge($WiPayment); 
                     
                    

                      return view('centremanager.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
            }  
         }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
       public function create(Request $request)
    {
        $id = $request->getadmissions;
        $userBranch = Auth::user()->branchs;

        $alb = branch::Where('branchname',$userBranch)->get();
        $directstudentsdata = leads::find($id);
        $cours = course::get();
        $leadsdata = leads::get();

        $studentdetails = students::get();
       
        $branchdetails = Branch::where('branchname',$userBranch)->get();
        $course = course::get();
        $taxesna = Tax::get();
           $ucats = UnviersitiesCategory::all();

         return view('centremanager.admissionprocess.create',compact('alb','cours','leadsdata','directstudentsdata','studentdetails','branchdetails','course','taxesna','ucats'));
        
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

        $sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";
        $bitolinvno = "0";
        $cvrublinvno = "0";
        $cvrukhinvno = "0";
        $rntuinvno = "0";
        $manipalinvno = "0";

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

        $inoviceno = explode("/",$invno);
       //dd($inoviceno);
        if($inoviceno[0] == 'INV-BITSJ')
        {
            $sjinvno = $inoviceno[3];

           
        }
        else if($inoviceno[0] == 'INV-BITMJ')
        {
            $mjinvno = $inoviceno[3];
         
        }
        elseif($inoviceno[0] == 'INV-BITWG')
        {
            $waginvno = $inoviceno[3];
        }

         elseif($inoviceno[0] == 'INV-BITOL')
        {
            $bitolinvno = $inoviceno[3];
        }

         elseif($inoviceno[0] == 'INV-CVRU(BL)')
        {
            $cvrublinvno = $inoviceno[3];
        }
        elseif($inoviceno[0] == 'INV-CVRU(KH)')
        {
            $cvrukhinvno = $inoviceno[3];
        }

         elseif($inoviceno[0] == 'INV-RNTU')
        {
            $rntuinvno = $inoviceno[3];
        }
        elseif($inoviceno[0] == 'INV-MANIPAL')
        {
            $manipalinvno = $inoviceno[3];
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
            'Ibranchs'=> $branchdata,
            'Invoiceno'=> $request->invno,
            'Isjno'=> $sjinvno,
            'Imjno'=> $mjinvno,
            'Iwgno'=> $waginvno,
            'Ibitolno'=> $bitolinvno,
            'Icvrublno'=> $cvrublinvno,
            'Icvrukhno'=> $cvrukhinvno,
            'Irntuno'=> $rntuinvno,
            'Imanipalno'=> $manipalinvno,
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


            return redirect('/centremanager-user-create-payment/'.$invoicesid);

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
            'Ibranchs'=> $branchdata,
            'Invoiceno'=> $request->invno,
            'Isjno'=> $sjinvno,
            'Imjno'=> $mjinvno,
            'Iwgno'=> $waginvno,
            'Ibitolno'=> $bitolinvno,
            'Icvrublno'=> $cvrublinvno,
            'Icvrukhno'=> $cvrukhinvno,
            'Irntuno'=> $rntuinvno,
            'Imanipalno'=> $manipalinvno,
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

                 

             return redirect('/centremanager-user-create-payment/'.$invoicesid);
                    

        }
    }

   
     public function admissionform($id)
    {


        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         //$installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         //$paymentdata = payment::where('inviceid',$id)->get();

        
        

        return view('centremanager.admissionprocess.admissionform',compact('aprocess','invvcoursed','univCourse'));

    }


      public function viewinvoice($id)
      {

         
        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");
         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');


        return view('centremanager.admissionprocess.generalinvoices',compact('aprocess','invvcoursed','installmentfees','univCourse'));
     }

    public function paymentstore(Request $request,$id,invoices $invoices,payment $payment)
    {

        
        $userId = Auth::user()->id;
        $studentsdata = $request->students;
        $rcepno = $request->receiptno;
        $ernos = $request->erno;
        $rptype = $request->ptypes;

            if($rptype == 'LumpSum')
            {
                $tmamount = $request->totalamount;
                $preceived = $request->paymentrecieved;
            }
            else if($rptype == 'EMI')
            {
                $tmamount = $request->instam;
                $preceived = $request->instam;
            }

        $receptsno = explode("/",$rcepno);
        $newerno = explode("/",$ernos);
       // dd($receptsno);

        $sjrecno = "0";
        $mjrecno = "0";
        $wagrecno = "0";
        $bitolrecno = "0";
        $cvrublrecno = "0";
        $cvrukhrecno = "0";
        $rnturecno = "0";
        $manipalrecno = "0";
        $elrecno = "0";

        $sjernocs = "0";
        $mjernocs = "0";
        $wagernocs = "0";
        $bitolernocs = "0";
        $cvrublernocs = "0";
        $cvrukhernocs = "0";
        $rntuernocs = "0";
        $manipalernocs = "0";
        $elernocs = "0";
       

        if($receptsno[0] == 'BITSJ')
        {
            $sjrecno = $receptsno[1];

            //dd($sjrecno);
        }
        else if($receptsno[0] == 'BITMJ')
        {
            $mjrecno = $receptsno[1];
         
        }
        elseif($receptsno[0] == 'BITWG')
        {
            $wagrecno = $receptsno[1];
        }
        
        elseif($receptsno[0] == 'BITEL')
        {
            $elrecno = $receptsno[1];
        }

         elseif($receptsno[0] == 'BITOL')
        {
            $bitolrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'CVRU(BL)')
        {
            $cvrublrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'CVRU (KH)')
        {
            $cvrukhrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'RNTU')
        {
            $rnturecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'MANIPAL')
        {
            $manipalrecno = $receptsno[1];
        }



         if($newerno[0] == 'BITSJ')
        {
            $sjernocs = $newerno[3];
            
        }
        else if($newerno[0] == 'BITMJ')
        {
            $mjernocs = $newerno[3];
         
        }
        elseif($newerno[0] == 'BITWG')
        {
            $wagernocs = $newerno[3];
        }
        
        elseif($newerno[0] == 'BITEL')
        {
            $elernocs = $newerno[3];
        }

         elseif($newerno[0] == 'BITOL')
        {
            $bitolernocs   = $newerno[3];
        }

        elseif($newerno[0] == 'CVRU(BL)')
        {
            $cvrublernocs  = $newerno[3];
        }

        elseif($newerno[0] == 'CVRU (KH)')
        {
           $cvrukhernocs  = $newerno[3];
        }

        elseif($newerno[0] == 'RNTU')
        {
            $rntuernocs  = $newerno[3];
        }

        elseif($newerno[0] == 'MANIPAL')
        {
            $manipalernocs    = $newerno[3];
        }

        //dd($request->all());

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'totalamount'=> $tmamount,
            'paymentreceived'=> $preceived,
            'remainingamount'=> $request->ramount,
            'paymentdate'=> $request->paymentdate,
            'paymentmode'=> $request->paymentmode,
            'nexamountdate'=> $request->remindersdates,
            'bankname'=> $request->bankname,
            'chequeno'=> $request->chequeno,
            'chequedate'=> $request->chequedate,
            'chequetype'=> $request->chequetype,
            'remarknoe'=> $request->remarknote,
            'userid'=> $userId,
            'studentsid'=> $request->students,
            'branchs'=> $request->brnavhc,
            'receiptno'=> $rcepno,
            'sjrecpno'=> $sjrecno,
            'mjrecpno'=> $mjrecno,
            'wgrecpno'=> $wagrecno,
            'elrecpno'=> $elrecno,
            'bitolrecpno'=> $bitolrecno,
            'cvrublrecpno'=> $cvrublrecno,
            'cvrukhrecpno'=> $cvrukhrecno,
            'rnturecpno'=> $rnturecno,
            'manipalrecpno'=> $manipalrecno,
            'studenterno'=> $ernos,
            'sjerno'=> $sjernocs,
            'mjerno'=> $mjernocs,
            'wgerno'=> $wagernocs,
            'elernos'=> $elernocs,
            'cvrukherno'=> $cvrukhernocs,
            'bitolerno'=> $bitolernocs,
            'rntuerno'=> $manipalernocs,
            'manipalerno'=> $manipalernocs,
            'studentadmissiionstatus'=> 'New Student',
            'installmentid'=> $request->installid,
            'paymentype' => $request->ptypes
        ]);

        $insid = $request->installid;

        $paymentid = $payment->id;

        $updatenew = admissionprocessinstallmentfees::find($insid);

        if($updatenew)
       {
            $updatenew->status = 1;
            $updatenew->save();
        }
        




        $updatesid = admissionprocess::find($id);
        $updatesid->status = '1';
        $updatesid->serno = $payment->studenterno;
        $updatesid->save();

        $studentsphone = admissionprocess::where('id',$id)->pluck('sphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
      
         //dd($leadupodat);
       if($leadupodat)
       {
            $leadupodat->conversationstatus = '1';
            $leadupodat->save();
        
       }
        



        return redirect('/center-manager-user-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
   {
            $coursesid = $request->admissionsid;
               // dd($coursesid);
            $data= array();

            $aprod = admissionprocess::find($coursesid);

        $abranch = $aprod->suniversities;
        //dd($abranch);

        if($abranch == 'BIT')
        {
             $result = admissionprocesscourses::select('courses.coursename','admissionprocesscourses.*')->leftjoin('courses','courses.id','=','admissionprocesscourses.courseid')->where('admissionprocesscourses.invid',$coursesid)->get();

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

             $result = admissionprocesscourses::select('courses.coursename','admissionprocesscourses.*')->leftjoin('courses','courses.id','=','admissionprocesscourses.univecoursid')->where('admissionprocesscourses.invid',$coursesid)->get();

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


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stundtsmanems)
    {
        $adproc = admissionprocess::where('id',$stundtsmanems)->get();

        echo json_encode($adproc);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function payment($id,admissionprocess $admissionprocess)
    {

        $paymentdetails = admissionprocess::find($id);
         $branc = Branch::all();
         $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
         $psource = PaymentSource::all();
        return view('centremanager.payments.create',compact('paymentdetails','branc','installmentfees','psource'));
    }


    public function paymentreceipt($id)
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

         $installdata = admissionprocessinstallmentfees::leftJoin('payments', 'payments.installmentid', '=', 'admissionprocessinstallmentfees.id')->where('admissionprocessinstallmentfees.invoid',$newId)->orderBy('admissionprocessinstallmentfees.id','DESC')->get();


        return view('centremanager.admissionprocess.paymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy()
    {
        $userBranch = Auth::user()->branchs;

       // dd($userBranch);
       
     
        
        $brnagch = Branch::all();
        $userALl = User::all();
             $currentMonth = date('m');
         /*$studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereMonth('admissionprocesses.sadate',$currentMonth)->orderBy('admissionprocesses.sadate','DESC')->groupBy('payments.inviceid')->get(); */
         
         $studentsdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereMonth('payments.paymentdate',$currentMonth)->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


       
         foreach($studentsdata as $studentpaymen)
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

          $newstudentsdata = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->where('re_admissions.rstobranches',$userBranch)->orderBy('payments.id','ASC')->groupBy('payments.reinviceid')->get(); 

       
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


         $invototal = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereMonth('admissionprocesses.sadate',$currentMonth)->sum('invtotal'); 
         
         $retotal = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->sum('rinvtotal'); 
          

         $sumtotal =  $invototal +  $retotal;

         $pamenreceived = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereMonth('admissionprocesses.sadate',$currentMonth)->sum('paymentreceived'); 
         

         
         $repaymreceived = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->sum('paymentreceived'); 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;

             $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
           $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        return view('centremanager.admissionprocess.studentsdetails',compact('studentsdata','brnagch','userALl','newstudentsdata','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));
    }

     public function branchwiseAdmission(Request $request)
     {

        $userBranch = Auth::user()->branchs;

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
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->where('payments.studenterno','!=',null)->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 
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

               $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$userBranch)->Where('re_admissions.rstudents', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
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

        

          return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->where('payments.studenterno','!=',null)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

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


          $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$userBranch)->Where('re_admissions.rsphone', $mobdatas)->orwhere('re_admissions.rswhatsappno',$mobdatas)->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
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

       

          return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         

          if($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.studenterno','DESC')->get(); 
              
              $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

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

               
               

                return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

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

            $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('readmissioncourses','readmissioncourses.reinvid','=','readmissioncourses.id')->join('payments', 'payments.inviceid', '=', 'readmissioncourses.id')->where('re_admissions.rstobranches',$userBranch)->where('readmissioncourses.recourseid',$coursedatas)->orWhere('readmissioncourses.reunivecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

       
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

         

          return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }

    


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


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
         

          return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }






      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

  

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

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
               
             

                return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

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
               
             

                return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
      }


      

      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
           //dd($findcourse);

           /* foreach($findcourse as $courses)
            {
                  $getourses = $courses->coursename;

            }*/

          //  dd($findcourse);

      

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->where('admissionprocesses.stobranches',$userBranch)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


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


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('readmissioncourses','readmissioncourses.reinvid','=','re_admissions.id')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$userBranch)->where('readmissioncourses.recourseid',$findcourse)->orWhere('readmissioncourses.reunivecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get();
       
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
               
              

                return view('centremanager.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate','ramesfinds'));
      }

   }

    public function filterPendingAdmission(Request $request)
     {

        $userBranch = Auth::user()->branchs;

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
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

          $namesfinds = \DB::table('admissionprocesses')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->select('admissionprocesses.*','admissionprocesses.id as aid')->where('admissionprocesses.stobranches',$userBranch)
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
        

           return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
         $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.stobranches',$userBranch)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->select('admissionprocesses.*','admissionprocesses.id as aid')
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

       

           return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

               

               $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->select('admissionprocesses.*','admissionprocesses.id as aid')
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
               

                 return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

               $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->select('admissionprocesses.*','admissionprocesses.id as aid')
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
               

                 return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
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
         

           return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        

         
           $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesses.admsisource',$sources)->whereBetween('admissionprocesses.sadate',[$starsdates,$enssdates])->select('admissionprocesses.*','admissionprocesses.id as aid')
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
         

           return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }





      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

  

      
          $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('admissionprocesses.sadate',[$asdates,$aenddates])->select('admissionprocesses.*','admissionprocesses.id as aid')
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
               
             

                 return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       

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
               
             

                 return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
      }


      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
           

         $namesfinds = \DB::table('admissionprocesses')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('admissionprocesses.sadate',[$cstartdate,$cenddate])->select('admissionprocesses.*','admissionprocesses.id as aid')
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
               
              

                 return view('centremanager.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      }

   }


     public function maildetails($id)
    {
        $data = array();
        $getadmissionsdetails = admissionprocess::find($id);
        $getadmissionscoursesdetails = admissionprocesscourses::select('courses.coursename','admissionprocesscourses.*')->Join('courses','courses.id','=','admissionprocesscourses.courseid')->where('admissionprocesscourses.invid',$id)->get();

        $data["StudentsName"] = $getadmissionsdetails->studentname;
        $data["Studentserno"] = $getadmissionsdetails->serno;
        $data["studentsemails"] = $getadmissionsdetails->semails;
        $data["Courses"] = $getadmissionscoursesdetails;
        
        $pdf = PDF::loadView('superadmin.invoice.mailinvoice',compact('getadmissionsdetails','getadmissionscoursesdetails'));
                     
                     Mail::send('superadmin.invoice.invoicesmails', $data, function ($message) use ($data, $pdf) {
            $data;
            $message->to($data["studentsemails"],$data["studentsemails"])
                ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Welcome letter to New Students.")
                ->attachData($pdf->output(),"Invoices.pdf");
        });

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect()->back()->with('success','Invoice Sent Successfully!!!');

                }
    }

    public function pendingAdmission()
     {

           $userBranch = Auth::user()->branchs;
         $currentMonth = date('m');
        /* $brnagch = Branch::all();
        $userALl = User::all();*/

        $userBranc = Auth::user()->branchs;
        /* $studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereNull('payments.inviceid')->groupBy('payments.inviceid')->get();*/
          
        $studentsdata = \DB::table('admissionprocesses')->where('stobranches',$userBranc)->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                ->orderBy('payments.id','DESC');
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



         $newStudents = \DB::table('re_admissions')->where('rstobranches',$userBranc)->select('re_admissions.*','re_admissions.id as aid')
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
          
           
           // $total = $invototal->sum('invtotal');

        //    dd($sumtotal);

             $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();


           return view('centremanager.admissionprocess.pendingadmission',compact('studentsdata','newStudents','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));
        
    }

    public function paymentreceiptlist($id)
    {
        $selectID = payment::where('inviceid',$id)->get();
        $admissiondet = admissionprocess::find($id);
        return view('centremanager.invoice.receiptlist',compact('selectID','admissiondet'));
    }

     public function repayment($id)
    {

        $paymentdetails = admissionprocess::find($id);
        $paymentsse = payment::where('inviceid',$id)->orderBy('id','DESC')->take(1)->get();
        $branc = Branch::all();
        $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
        return view('centremanager.invoice.repayment',compact('paymentdetails','branc','installmentfees','paymentsse','psource'));
    }


     public function restorepayment(Request $request,$id,invoices $invoices,payment $payment)
    {

        $latestincrme = payment::where('inviceid',$id)->latest()->get()->pluck('instid');
         $counts = isset($latestincrme[0]) ? $latestincrme[0] : false;
           // $counts = $mj + 1;
        $incrementid = $counts + 1;
        /*dd($incrementid);*/
       
        $userId = Auth::user()->id;
        $studentsdata = $request->students;
        $rcepno = $request->receiptno;
        $ernos = $request->erno;
        $rptype = $request->ptypes;

            if($rptype == 'LumpSum')
            {
                $tmamount = $request->totalamount;
                $preceived = $request->paymentrecieved;
            }
            else if($rptype == 'EMI')
            {
                $tmamount = $request->instam;
                $preceived = $request->instam;
            }

        $receptsno = explode("/",$rcepno);
        $newerno = explode("/",$ernos);
       // dd($receptsno);

        $sjrecno = "0";
        $mjrecno = "0";
        $wagrecno = "0";
        $bitolrecno = "0";
        $cvrublrecno = "0";
        $cvrukhrecno = "0";
        $rnturecno = "0";
        $manipalrecno = "0";

       

        if($receptsno[0] == 'BITSJ')
        {
            $sjrecno = $receptsno[1];

            //dd($sjrecno);
        }
        else if($receptsno[0] == 'BITMJ')
        {
            $mjrecno = $receptsno[1];
         
        }
        elseif($receptsno[0] == 'BITWG')
        {
            $wagrecno = $receptsno[1];
        }

         elseif($receptsno[0] == 'BITOL')
        {
            $bitolrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'CVRU(BL)')
        {
            $cvrublrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'CVRU (KH)')
        {
            $cvrukhrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'RNTU')
        {
            $rnturecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'MANIPAL')
        {
            $manipalrecno = $receptsno[1];
        }

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'instid'=> $incrementid,
            'totalamount'=> $tmamount,
            'paymentreceived'=> $preceived,
            'remainingamount'=> $request->ramount,
            'paymentdate'=> $request->paymentdate,
            'paymentmode'=> $request->paymentmode,
            'bankname'=> $request->bankname,
            'paymentype' => $request->ptypes,
            'nexamountdate'=> $request->remindersdates,
            'chequeno'=> $request->chequeno,
            'chequedate'=> $request->chequedate,
            'chequetype'=> $request->chequetype,
            'remarknoe'=> $request->remarknote,
            'userid'=> $userId,
            'studentsid'=> $request->students,
            'branchs'=> $request->brnavhc,
            'receiptno'=> $rcepno,
            'sjrecpno'=> $sjrecno,
            'mjrecpno'=> $mjrecno,
            'wgrecpno'=> $wagrecno,
            'bitolrecpno'=> $bitolrecno,
            'cvrublrecpno'=> $cvrublrecno,
            'cvrukhrecpno'=> $cvrukhrecno,
            'rnturecpno'=> $rnturecno,
            'manipalrecpno'=> $manipalrecno,
            'studentadmissiionstatus'=> 'New Student',
            'installmentid'=> $request->installid,
        ]);

        $insid = $request->installid;

        $paymentid = $payment->id;

        $updatenew = admissionprocessinstallmentfees::find($insid);

        if($updatenew)
       {
            $updatenew->status = 1;
            $updatenew->save();
        }
        




        $updatesid = admissionprocess::find($id);
        $updatesid->status = '1';
        $updatesid->save();

        $studentsphone = admissionprocess::where('id',$id)->pluck('sphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
      
         //dd($leadupodat);
       if($leadupodat)
       {
            $leadupodat->conversationstatus = '1';
            $leadupodat->save();
        
       }
        



        return redirect('/center-manager-user-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }
}
