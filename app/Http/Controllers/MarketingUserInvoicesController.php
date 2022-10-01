<?php

namespace App\Http\Controllers;
use App\invoices;
use App\students;
use App\Branch;
use App\course;
use App\Source;
use App\invoicescourses;
use App\invoicesinstallmentfees;
use App\admissionprocessinstallmentfees;
use App\PaymentSource;
use App\payment;
use App\admissionprocess;
use App\leads;
use App\Tax;
use App\followup;
use App\coursecategory;
use App\User;
use Auth;
use DB;
use Mail;
use Illuminate\Http\Request;

class MarketingUserInvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentreceiptlists($id)
    {
      $result = payment::where('inviceid',$id)->orderBy('id','DESC')->get();

      $admissionname = admissionprocess::find($id);

      return view('marketing.invoice.paymentreceiptlist',compact('result','admissionname'));

    }
    
   public function totalfees()
    {

        $userId = Auth::user()->id;

        $cour = course::all();
        $sourcedata = Source::get();
        $folss = followup::get();
        $userBranch = Auth::user()->branchs;
        $userdata = User::where('id', $userId)->get();

        $userBranch = Auth::user()->branchs;
        $currentMonth = date('m');

        $invoicesdata = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$userId)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

         $reinvoicesdata = "";

        
        $cour = course::all();
        $branchdata = Branch::where('branchname', $userBranch)->get();
        $userdata = User::where('id', $userId)->get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();
        $folss = followup::get();

        return view('marketing.invoice.totalfees', compact('invoicesdata','reinvoicesdata', 'cour', 'sourcedata', 'folss', 'userdata','cour', 'sourcedata', 'folss', 'branchdata', 'ccatall', 'userdata'));
    }

   public function filterfeesdatas(Request $request)
    {

        $userBranch = Auth::user()->branchs;
        $userId = Auth::user()->id;
        $datesfor = "";
        $namedatas = "";
        $mobdatas = "";
        $coursedatas = "";
        $cmodes = "";
        $sources = "";
        $fsearch = "";
        $asearch = "";
        $bransdata = "";
        $categorydata = "";

        if ($namedatas = $request->getstudentsnames)
        {
            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

              $namesfinds = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as aid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.admissionsusersid',$userId)->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->get();

                $reinvoicesdata = "";

            return view('marketing.invoice.filterfees', compact('namesfinds','reinvoicesdata','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata'));
        }

        elseif ($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('branchs', $userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();


             $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid', $userId)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orderBy('payments.id','DESC')->get();

              $reinvoicesdata = "";

            return view('marketing.invoice.filterfees', compact('namesfinds','reinvoicesdata','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata'));
        }

        elseif ($datesfor = $request->DateFor)
        {
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if ($datesfor == "Admission Date")
            {

                $folss = followup::get();
                $userdata = User::where('id', $userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $branchdata = Branch::where('branchname', $userBranch)->get();
                $ccatall = coursecategory::get();

                $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$userId)->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();

                 $reinvoicesdata = "";

                return view('marketing.invoice.filterfees', compact('namesfinds','reinvoicesdata','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'startdates', 'enddats'));
            }

            elseif ($datesfor == "Payment Date")
            {

                $folss = followup::get();
                $userdata = User::where('id', $userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $branchdata = Branch::where('branchname', $userBranch)->get();
                $ccatall = coursecategory::get();

                 $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$userId)->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();

                 $reinvoicesdata = "";

                return view('marketing.invoice.filterfees', compact('namesfinds','reinvoicesdata','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'startdates', 'enddats'));
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
                
                  $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid', $userId)->where('admissionprocesscourses.courseid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = "";
               

                return view('marketing.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));

               }


               else
               {
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid', $userId)->where('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = "";
               

                return view('marketing.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
               }


      }

       

        elseif ($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid', $userId)->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.id','DESC')->get();

            $reinvoicesdata = "";

            return view('marketing.invoice.filterfees', compact('namesfinds','reinvoicesdata','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'starsdates', 'enssdates'));
        }

       
        elseif ($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

          

                 $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->orderBy('payments.id','DESC')->get();

            $reinvoicesdata = "";

            return view('marketing.invoice.filterfees', compact('reinvoicesdata','namesfinds','reinvoicesdata','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'asdates', 'aenddates'));
        }

        elseif ($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::where('branchs', $userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

                $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->where('admissionprocesses.stobranches',$bransdata)->orderBy('payments.id','DESC')->get();

                 $reinvoicesdata = "";

            return view('marketing.invoice.filterfees', compact('namesfinds','reinvoicesdata','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'bstartdate', 'benddate'));
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

                     
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid', $userId)->whereIn('admissionprocesscourses.courseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = ""; 
                     
                    

                      return view('marketing.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }


                  else
                  {
                    

                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid', $userId)->whereIn('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = ""; 
                     
                    

                      return view('marketing.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }
           
          }
    }

    public function pendingamount()
    {
        $userId = Auth::user()->id;
        $userBranch = Auth::user()->branchs;
        //   $admiId = admissionprocess::pluck('id');
        $currentMonth = date('m');

        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
            ->whereMonth('payments.paymentdate', $currentMonth)->where('admissionprocesses.admissionsusersid', $userId)->groupBy('payments.inviceid')
            ->get();

        //dd($WiPayment->precives);
        $ReWiPayment = 0;

        $pendamount = $WiPayment;
        $rependamount = $ReWiPayment;

        $invototal = $pendamount->sum('invtotal');

        $retotal = 0;

        $sumtotal = $invototal + $retotal;

        $pamenreceived = $pendamount->sum('precives');;

        $repaymreceived = 0;

        $totslreceived = $pamenreceived + $repaymreceived;

        $remainingamount = $sumtotal - $totslreceived;

        $folss = followup::get();
        $userdata = User::where('id', $userId)->get();
        $cour = course::all();
        $sourcedata = Source::all();
        $branchdata = Branch::where('branchname', $userBranch)->get();
        $ccatall = coursecategory::get();

        //  dd($pendamount);
        return view('marketing.invoice.pendingamount', compact('pendamount', 'rependamount', 'sumtotal', 'totslreceived', 'remainingamount','folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall'));

    }

    public function filtermakretingpendingfees(Request $request)
     {
        $userBranch = Auth::user()->branchs;
        $userId = Auth::user()->id;

        $datesfor = "";
        $namedatas = "";
        $mobdatas = "";
        $coursedatas = "";
        $cmodes = "";
        $sources = "";
        $fsearch = "";
        $asearch = "";
        $bransdata = "";
        $categorydata = "";

        if ($namedatas = $request->getstudentsnames)
        {
            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

            //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();
            $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid', $userId)->Where('admissionprocesses.studentname', 'like', '%' . $namedatas . '%')->groupBy('payments.inviceid')
                ->get();

            return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata'));
        }

        elseif ($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
            $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid', $userId)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno', $mobdatas)->groupBy('payments.inviceid')
                ->get();

            return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata'));
        }

        elseif ($datesfor = $request->DateFor)
        {
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if ($datesfor == "Admission Date")
            {

                $folss = followup::get();
                $userdata = User::where('id', $userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $branchdata = Branch::where('branchname', $userBranch)->get();
                $ccatall = coursecategory::get();

                $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid', $userId)->whereBetween('admissionprocesses.sadate', [$startdates, $enddats])->groupBy('payments.inviceid')
                    ->get();

                return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'startdates', 'enddats'));
            }

            elseif ($datesfor == "Payment Date")
            {

                $folss = followup::get();
                $userdata = User::where('id', $userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $branchdata = Branch::where('branchname', $userBranch)->get();
                $ccatall = coursecategory::get();

                $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid', $userId)->whereBetween('payments.paymentdate', [$startdates, $enddats])->groupBy('payments.inviceid')
                    ->get();

                return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'startdates', 'enddats'));
            }

        }

        elseif ($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();
            $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                ->leftJoin('admissionprocesscourses', 'admissionprocesscourses.invid', '=', 'admissionprocesses.id')
                ->where('admissionprocesses.admissionsusersid', $userId)->where('admissionprocesscourses.courseid', $coursedatas)->orWhere('admissionprocesscourses.univecoursid', $coursedatas)->whereBetween('payments.paymentdate', [$cstartsdates, $cendsdates])->groupBy('payments.inviceid')
                ->get();

            return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'cstartsdates', 'cendsdates'));
        }

        elseif ($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                ->where('admissionprocesses.admsisource', $sources)->where('admissionprocesses.admissionsusersid', $userId)->whereBetween('payments.paymentdate', [$starsdates, $enssdates])->groupBy('payments.inviceid')
                ->get();

            return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'starsdates', 'enssdates'));
        }

        elseif ($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                ->where('admissionprocesses.admissionsusersid', $asearch)->whereBetween('payments.paymentdate', [$asdates, $aenddates])->groupBy('payments.inviceid')
                ->get();

            return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'asdates', 'aenddates'));
        }

        

        elseif ($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::where('id', $userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname', $userBranch)->get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id', $categorydata)->pluck('id');

            $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*', 'admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                ->leftJoin('admissionprocesscourses', 'admissionprocesscourses.invid', '=', 'admissionprocesses.id')
                ->where('admissionprocesses.admissionsusersid', $userId)->where('admissionprocesscourses.courseid', $findcourse)->orWhere('admissionprocesscourses.univecoursid', $findcourse)->whereBetween('payments.paymentdate', [$cstartdate, $cenddate])->groupBy('payments.inviceid')
                ->get();

            return view('marketing.invoice.filterpendingamount', compact('namesfinds', 'folss', 'userdata', 'cour', 'sourcedata', 'branchdata', 'ccatall', 'datesfor', 'namedatas', 'mobdatas', 'coursedatas', 'cmodes', 'sources', 'fsearch', 'asearch', 'bransdata', 'categorydata', 'cstartdate', 'cenddate'));
        }

    }

     public function repayment($id)
    {

        $paymentdetails = admissionprocess::find($id);
        $paymentsse = payment::where('inviceid',$id)->orderBy('id','DESC')->take(1)->get();
        $branc = Branch::all();
        $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
        return view('marketing.invoice.repayment',compact('paymentdetails','branc','installmentfees','paymentsse','psource'));
    }

    public function restorepayment(Request $request,$id)
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
        



        return redirect('/marketing-user-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }


     public function totalinvoices()
    {   

        /*$student = admissionprocess::all();*/
        //   $admiId = admissionprocess::pluck('id');
          $userId = Auth::user()->id;
         $currentMonth = date('m');
          $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->whereMonth('admissionprocesses.sadate',$currentMonth)
          ->where('admissionprocesses.admissionsusersid',$userId)
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('admissionprocesses.id','DESC')->get(); 


        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->whereMonth('payments.paymentdate',$currentMonth)
         ->where('admissionprocesses.admissionsusersid',$userId)
         ->groupBy('payments.inviceid')
         ->orderBy('admissionprocesses.id','DESC')
         ->get();


         
        
        $pendamount = $NewPayment->merge($WiPayment);
        $rependamount = 0;

        $invototal = $pendamount->sum('invtotal');
        
        $retotal = 0;

        //dd($invototal);

        $sumtotal = $invototal + $retotal;
          
         $pamenreceived = $pendamount->sum('paymentreceived');
         

         
         $repaymreceived = 0; 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;


      //  dd($pendamount);
        //return view('superadmin.invoice.pendingamount',compact('pendamount'));
        
        return view('marketing.invoice.totalinvoice',compact('pendamount','rependamount','sumtotal','totslreceived','remainingamount'));
    }


    public function filterfees(Request $request)
      {



        $dfors = $request->DateFor;



        if($cdatas = $request->coursedatas)
        {

             $userId = Auth::user()->id;

            $dsearch = $request->datesearch;
            $ensdsearch = $request->enddatesearch;
            $uSerId = Auth::user()->id;
                $mdatas ="";
                $mobilefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc="";
                $dfors ="";
                $reinovicesdata ="";
                $remobilefinds  ="";
            //$coursefinds="";
            
                $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                 $userdata = User::where('id',$userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

               
               $courseFindsdata = course::where('id',$cdatas)->pluck('coursename');
               //dd($courseFindsdata);

               $coursefinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesscourses.courseid',$cdatas)->where('admissionprocesses.admissionsusersid',$userId)->whereBetween('admissionprocesses.sadate',[$dsearch, $ensdsearch])->get();

              // dd($coursefinds);
                                                                              
              

                                  

            

              return view('marketing.invoice.filterfees',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata','dfors','remobilefinds','courseFindsdata','dsearch','ensdsearch'));

        }

        else if($mdatas = $request->mobilefilters)
        {
            $uSerId = Auth::user()->id;
            $cdatas ="";
            $coursefinds="";
            $sourcesFind="";
            $CourseModeFInd="";
            $FollowupsFind="";
            $datewiseSearc="";
            $AssinedSearch= "";
            $dfors ="";

            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                 $userdata = User::where('id',$uSerId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();


                $mobilefinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$uSerId)->where('admissionprocesses.sphone',$mdatas)->orWhere('admissionprocesses.swhatsappno',$mdatas)->get();
                                                                              
               //$remobilefinds = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as remid')->join('payments','payments.reinviceid','=','re_admissions.id')->where('re_admissions.rsphone',$mdatas)->orWhere('re_admissions.rswhatsappno',$mdatas)->get();


              //return view('superadmin.leads.filtersleads',compact('userdata','Cdates','cour','folss','mdatas','cdatas','mobilefinds','coursefinds'));
            return view('marketing.invoice.filterfees',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata','datewiseSearc','dfors','mdatas'));
        }


        else if($sourcessearch = $request->sourceSearch)
        {
           // dd($sourcessearch);
            $uSerId = Auth::user()->id;
                $dfors  = "";
                $dsearch = $request->datesearch;
                $ensdsearch = $request->enddatesearch;
                $uSerId = Auth::user()->id;
                $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc="";
                $remobilefinds = "";
                 $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                 $userdata = User::where('id',$userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();
                $courseFindsdata  = "";
                $cmodessearch ="";


              $sourcesFind = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$uSerId)->where('admissionprocesses.admsisource',$sourcessearch)->whereBetween('admissionprocesses.sadate',[$dsearch, $ensdsearch])->get();

               //$resourcesFind = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as remid')->join('payments','payments.reinviceid','=','re_admissions.id')->where('re_admissions.radmsisource',$sourcessearch)->whereBetween('re_admissions.rsadate',[$dsearch, $ensdsearch])->get();

             return view('marketing.invoice.filterfees',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata','sourcesFind','dfors','remobilefinds','sourcessearch','dsearch','ensdsearch','courseFindsdata','cmodessearch'));

        }

        else if($cmodessearch = $request->CourseModeSearch)
        {

             $uSerId = Auth::user()->id;
            $dfors  = "";
            $dsearch = $request->datesearch;
            $ensdsearch = $request->enddatesearch;

                //$uSerId = Auth::user()->id;
                $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc="";
                $remobilefinds="";
                $courseFindsdata ="";


            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                 $userdata = User::where('id',$userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                 $CourseModeFInd = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesscourses.coursemode',$cmodessearch)->where('admissionprocesses.admissionsusersid',$uSerId)->whereBetween('admissionprocesses.sadate',[$dsearch, $ensdsearch])->get();

               //$reCourseModeFInd = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as remid')->join('payments','payments.reinviceid','=','re_admissions.id')->join('readmissioncourses','readmissioncourses.reinvid','=','re_admissions.id')->where('readmissioncourses.recoursemode',$cmodessearch)->whereBetween('re_admissions.rsadate',[$dsearch, $ensdsearch])->get();
         

            
              return view('marketing.invoice.filterfees',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata','CourseModeFInd','dfors','remobilefinds','cmodessearch','dsearch','ensdsearch','courseFindsdata'));

        }

       

        else if($Asearch = $request->AssignedToSearch)
        {
             $uSerId = Auth::user()->id;
             $dsearch = $request->datesearch;
            $ensdsearch = $request->enddatesearch;
            $dfors = "";
            $cdatas ="";
            $mdatas ="";
            $mobilefinds="";
            $coursefinds="";
            $sourcesFind="";
            $CourseModeFInd="";
            $FollowupsFind="";
            $datewiseSearc="";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                 $userdata = User::where('id',$userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();
                $remobilefinds  = "";

                $courseFindsdata = "";
                $cmodessearch  = "";
                $sourcessearch  = "";
               

                $userGet = User::where('id',$Asearch)->pluck('name');

                  $AssinedSearch = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$Asearch)->whereBetween('admissionprocesses.sadate',[$dsearch, $ensdsearch])->get();

            /*  dd($CourseModeFInd);*/
                                                                              
              // $reAssinedSearch = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as remid')->join('payments','payments.reinviceid','=','re_admissions.id')->where('re_admissions.ruserid',$Asearch)->whereBetween('re_admissions.rsadate',[$dsearch, $ensdsearch])->get();
               

                return view('marketing.invoice.filterfees',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata','dfors','remobilefinds','userGet','dsearch','ensdsearch','courseFindsdata','cmodessearch','sourcessearch'));
        }

        else if($dfors == "Admission Date")
        {

            
            $uSerId = Auth::user()->id;
            $dsearch = $request->datesearch;
              $ensdsearch = $request->enddatesearch;
            $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc = "";            
                $reinovicesdata = "";            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                 $userdata = User::where('id',$userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $remobilefinds ="";
             

                $datewiseSearc = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$uSerId)->whereBetween('admissionprocesses.sadate',[$dsearch, $ensdsearch])->get();
                                                                              
               //$reinovicesdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as remid')->join('payments','payments.reinviceid','=','re_admissions.id')->whereBetween('re_admissions.rsadate',[$dsearch, $ensdsearch])->get();


               

              return view('marketing.invoice.filterfees',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata','dfors','remobilefinds','dfors','dsearch','ensdsearch'));



        }

        else if($dfors == "Payment Date")
        {

            
            $uSerId = Auth::user()->id;
            $dsearch = $request->datesearch;
              $ensdsearch = $request->enddatesearch;
            $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                 $datewiseSearc = "";            
                $reinovicesdata = "";
                $remobilefinds = "";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                 $userdata = User::where('id',$userId)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();


             

                $datewiseSearc = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->join('payments','payments.inviceid','=','admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$uSerId)->whereBetween('payments.paymentdate',[$dsearch, $ensdsearch])->get();
                                                                              
             //  $reinovicesdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as remid')->join('payments','payments.reinviceid','=','re_admissions.id')->whereBetween('payments.paymentdate',[$dsearch, $ensdsearch])->get();
               

              return view('marketing.invoice.filterfees',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata','dfors','remobilefinds','dfors','dsearch','ensdsearch'));



        }
    }

     
    public function index()
    {
        
         $userId = Auth::user()->id;
        //$studentsdata = admissionprocess::where('userId',$userId)->get();
        $invoicesdata = admissionprocess::select("admissionprocesses.*","payments.paymentreceived","payments.paymentdate","payments.paymentmode","payments.remainingamount","courses.coursename")->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->join('leads', 'leads.phone', '=', 'admissionprocesses.sphone')->join('admissionprocesscourses', 'admissionprocesscourses.invid', '=', 'admissionprocesses.id')->join('courses', 'admissionprocesscourses.courseid', '=', 'courses.id')->where('leads.user_id',$userId)->get();

                return view('marketing.invoice.invoicesdata',compact('invoicesdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        
        $sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";
        $discoun = "NULL";

        $studentname = $request->sname;
        $idate = $request->invoicedate;
        $ddate = $request->duedate;
        $branchdata = $request->brnach;
        $invno = $request->invno;
        $pmode = $request->paymentmode;
        $dtype = $request->discounttype;
        $subto = $request->subtotal;
        $tot = $request->total;
        $raxe = $request->taxs;
       
        $discoun2 = $request->discount2;

        $inoviceno = explode("/",$invno);
       
        if($inoviceno[0] == 'Inv-BITSJ')
        {
            $sjinvno = $inoviceno[3];

           
        }
        else if($inoviceno[0] == 'Inv-BITMJ')
        {
            $mjinvno = $inoviceno[3];
         
        }
        elseif($inoviceno[0] == 'Inv-BITWG')
        {
            $waginvno = $inoviceno[3];
        }


        if($dtype == "2")
        {
             $discoun = $request->discount1;
        }

        elseif($dtype == "1")
        {
            $discoun = $request->discount2;
        }

        if($pmode == "EMI") 

        {
            $invoicesmodel = new invoices();
                    $invoices = $invoicesmodel->create([
                        'studentid' => $studentname,
                        'branchId' => $branchdata,
                        'branchInvno' => $invno,
                        'sjIno' => $sjinvno,
                        'mjIno' => $mjinvno,
                        'wgIno' => $waginvno,
                        'discounttype' => $dtype,
                        'paymentmode' => $pmode,
                        'invdate' => $idate,
                        'duedate' => $ddate,
                        'subtotal' => $subto,
                        'invtotal' => $tot,
                        'discount' => $discoun,
                        'taxes' => $raxe,
                        'userid' => $userId,

                    ]);

                    $invoicesid = $invoices->id;
                    $coursesdata = $request->invcourse;
                    $courseprice = $request->invprice;
                    $csmode = $request->coursdataemode;
                    $cd = $request->duration;
                    $ct = $request->tax;
                    $installdate = $request->installmentdate;
                    $installprice = $request->installmentprice;
                    $pamount = $request->pendingamount;

                    for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $invoicescourses = new invoicescourses([
                                
                                'invid' => $invoicesid,
                                'courseid'   => $coursesdata[$i],
                                'coursemode'   => $csmode[$i],
                                'courseprice'   => $courseprice[$i],
                                
                            ]);
                            $invoicescourses->save();
                    }

                    for($k=0; $k <(count($installdate)); $k++)
                    {
                        $invoicesinstallmentfees = new invoicesinstallmentfees([
                            
                            'invoid' => $invoicesid,
                            'invoicedate'   => $installdate[$k],
                            'installmentamount'   => $installprice[$k],
                            'pendinamount'   => $pamount[$k],

                        ]);

                         $invoicesinstallmentfees->save();  
                    }


            return redirect('/view-marketing-user-invoice/'.$invoicesid);

        }

        else
        {


                    $invoicesmodel = new invoices();
                    $invoices = $invoicesmodel->create([
                        'studentid' => $studentname,
                        'branchId' => $branchdata,
                        'branchInvno' => $invno,
                        'sjIno' => $sjinvno,
                        'mjIno' => $mjinvno,
                        'wgIno' => $waginvno,
                        'discounttype' => $dtype,
                        'paymentmode' => $pmode,
                        'invdate' => $idate,
                        'duedate' => $ddate,
                        'subtotal' => $subto,
                        'invtotal' => $tot,
                        'discount' => $discoun,
                        'taxes' => $raxe,
                        'userid' => $userId,

                    ]);

                    $invoicesid = $invoices->id;
                    $coursesdata = $request->invcourse;
                    $courseprice = $request->invprice;
                    $cd = $request->duration;
                    $ct = $request->tax;

                    for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $invoicescourses = new invoicescourses([
                                
                                'invid' => $invoicesid,
                                'courseid'   => $coursesdata[$i],
                                'courseprice'   => $courseprice[$i],
                                'durations'   => $cd[$i],
                                'tax'   => $ct[$i],
                            ]);
                            $invoicescourses->save(); 
                    }

                 
                  
                return redirect('/view-marketing-user-invoice/'.$invoicesid)->with('success','Invoice Created Successfully!!');


        }
    }



         public function paymentreceipt($id,invoices $invoices,leads $leads)
    {

         //DB::select('SELECT * FROM  invoices WHERE id = $');

        $invda = invoices::find($id);


        $invoicesdetailsdata = DB::select('SELECT * FROM  invoices i, students s, branches b WHERE s.id = i.studentid AND  b.id = i.branchId AND i.id = "'.$id.'"'); 
        $invoicescoursedetails = DB::select('SELECT * FROM invoicescourses d, courses c WHERE  c.id = d.courseid AND d.invid = "'.$id.'" ');

        $installmentfees = DB::select("SELECT * FROM invoicesinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

        $paymenttotla = DB::select('SELECT * FROM invoices i, payments p WHERE  i.id = p.inviceid AND p.inviceid = "'.$id.'" ');


        $updateconversions = DB::select('SELECT studentname FROM invoices i, students s, payments p  WHERE s.id = i.studentid AND i.id = p.inviceid  AND p.inviceid=  "'.$id.'" ');

        //dd($updateconversions);

        //$UpdateDetails =  leads::where('studentname', $updateconversions)->update(['conversationstatus' => '1']);
         //$UpdateDetails = DB::table('leads')->where('studentname', $updateconversions)->update(array('conversationstatus' => '1'));

       // $updatedetails = DB::table("UPDATE leads SET conversationstatus = '1' WHERE studentname = '".$updateconversions."'");

       

       // dd($UpdateDetails);

        /*$updateconverted = DB::update('UPDATE ')*/

        //
        

        return view('marketing.invoice.paymentreceipt',compact('invda','invoicesdetailsdata','invoicescoursedetails','installmentfees','paymenttotla'));

    }

    public function payment($id,invoices $invoices)
    {

        $paymentdetails = invoices::find($id);
        return view('marketing.payments.create',compact('paymentdetails'));
    }


    public function paymentstore(Request $request,$id,invoices $invoices,payment $payment)
    {

        $userId = Auth::user()->id;
        $studentsdata = $request->students;

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'totalamount'=> $request->totalamount,
            'paymentreceived'=> $request->paymentrecieved,
            'remainingamount'=> $request->ramount,
            'paymentdate'=> $request->paymentdate,
            'paymentmode'=> $request->paymentmode,
            'bankname'=> $request->bankname,
            'chequeno'=> $request->chequeno,
            'chequedate'=> $request->chequedate,
            'chequetype'=> $request->chequetype,
            'remarknoe'=> $request->remarknote,
            'userid'=> $userId,
            'studentsid'=> $request->students,
        ]);

        $updatesid = invoices::find($id);
        $updatesid->status = '1';
        $updatesid->save();

        $studentsname = students::where('id',$studentsdata)->pluck('studentname');
        $leadupodat = leads::where('studentname',$studentsname)->first();
        $leadupodat->conversationstatus = '1';
        $leadupodat->save();



        //$paymentid = $payment->id;


        return redirect('/marketing-user-paymentreceipt/'.$id)->with('success','Payment Successfully Done!!!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function view($id,invoices $invoices)
    {

        $invda = invoices::find($id);

    $invoicesdetailsdata = DB::select('SELECT * FROM  invoices i, students s, branches b WHERE s.id = i.studentid AND  b.id = i.branchId AND i.id = "'.$id.'"'); 

         $invoicescoursedetails = DB::select('SELECT * FROM invoicescourses d, courses c WHERE  c.id = d.courseid AND d.invid = "'.$id.'" ');

        $installmentfees = DB::select("SELECT * FROM invoicesinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");
         
        return view('marketing.invoice.invoice_details',compact('invoicesdetailsdata','invoicescoursedetails','installmentfees','invda'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
}
