<?php
namespace App\Http\Controllers;

use App\course;
use App\Source;
use App\followup;
use App\User;
use App\Accounting;
use App\payment;
use App\TargetAlloted;
use App\assigntarget;
use App\IncentiveReleasePayments;
use App\PaymentSource;
use App\ExpenseCategory;
use App\ReAdmission;
use App\admissionprocess;
use App\CashExpense;
use App\admissionprocesscourses;
use App\CvruFees;
use App\clearaccounting;
use App\User_Salary_Deductions;
use App\SalaryCalculations;
use App\ChequeAgainstMoney;
use App\Branch;
use Carbon\CarbonPeriod;
use DB;
use Auth;
use Illuminate\Http\Request;

class CollectionReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editchequedetails($id)
    {
        $getchequedetails = payment::find($id);
        $ex = ExpenseCategory::all();

        return view('superadmin.accounting.edchequedetails',compact('getchequedetails','ex'));
    }
    
    public function editnotchequedetails($id)
    {
        $getchequedetails = payment::find($id);
       $students = admissionprocess::find($getchequedetails->inviceid);
       $restudents = ReAdmission::find($getchequedetails->reinviceid);
       $psource  = PaymentSource::all();
        //$ex = ExpenseCategory::all();

        return view('superadmin.accounting.editchequenotcleardetails',compact('getchequedetails','students','restudents','psource'));
    }

    public function updatenotchequedetails($id)
    {
        $updates = payment::find($id);

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
                $update->paymentmode = $request->paymentmode;
                $update->bankname = $request->bankname;
                $update->chequeno = $request->chequeno;
                $update->chequedate = $request->chequedate; 
                $update->chequetype = $request->chequetype;
                $update->rdatess = $request->paymentsdates;
                $update->revisedpaymentsmodes = $request->paymentmode;
                $update->revisedpaymentsstatus = 1;
                $update->chequestatus = 1;
                $update->paymentreceived = $request->gpayableamount;
                $update->remainingamount = $request->gremainingamount; 
                $update->nexamountdate = $request->gnxtamountdate; 

                $update->save(); 
            }

             else
            {
                
                //$update->paymentdate = $request->paymentsdates;
                //$update->reviseddates = $request->paymentsdates;
                $update->rdatess = $request->paymentsdates;
                $update->revisedpaymentsmodes = $request->paymentmode;
                $update->revisedpaymentsstatus = 1;
                $update->chequestatus = 1;
                $update->paymentreceived = $request->gpayableamount;
                $update->remainingamount = $request->gremainingamount; 
                $update->nexamountdate = $request->gnxtamountdate; 
                $update->save(); 
            }
    }

    public function updatechequedetails($id,Request $request)
    {
        $updatescheque = payment::find($id);
        $updatescheque->chequedepositsto = $request->ccchequedepositsto;
        $updatescheque->chequeremarsk = $request->remarks;
        $updatescheque->save();

        return redirect('/bank-accounting-details/'.$updatescheque->paymentdate)->with('success','Cheque Updated Successfully');

    } 
    
    public function editcvrufees($id,$barnchs)
    {
        $getcvrufees = CvruFees::find($id);
        $getpaymentdate = payment::find($getcvrufees->studentid);

        return view('superadmin.accounting.editcvrufees',compact('getcvrufees','barnchs','getpaymentdate'));

        //return view('superadmin.accounting.editcvrufees',compact('getcvrufees','barnchs'));
    }

    public function updatescvrufees($id,$barnchs,Request $request)
    {
        $updatesfees = CvruFees::find($id);
        $updatesfees->cpaymentdate  = $request->paymentsdate;
        $updatesfees->sverno  = $request->studenterono;
        $updatesfees->cvrufees  = $request->cvrufees;
        $updatesfees->bitfees  = $request->bitfees;
        $updatesfees->studentsnames  = $request->studentsnames;
        $updatesfees->coursenames  = $request->coursenames;
        $updatesfees->admissionsfors  = $request->admissionfors;
        $updatesfees->totalfees  = $request->totaalsfees;
        $updatesfees->tbalancefees  = $request->blacncessfees;
        $updatesfees->payablefees  = $request->payabdlefees;
        $updatesfees->universityfees  = $request->univefess;
        $updatesfees->releeaseddates  = $request->releaseddates;
        $updatesfees->preceiptnos  = $request->receiptesnos;
       
        $updatesfees->save();

        return redirect('/cvrufees-details/'.$request->paymentsdate.'/'.$barnchs)->with('success','Cvru Fees Updated!!');
    }
    
    public function cashclearence(Request $request)
    {
        $cdatess = $request->cashdates;
        $cbranchsed = $request->casbranchs;
        $caccountifnd = clearaccounting::whereDate('accountingdates',$cdatess)->first();

        $dakmsm = clearaccounting::create(['accountingdates' => $cdatess,'clbranchs' => $cbranchsed,'cashclearence' => 1]);


        return redirect()->back()->with('success','Cash Accounting Clear!!');

    }

    public function cvrupaymentcleareence(Request $request)
    {
        $cvdates = $request->cvrudates;
        $brnahcs = $request->cbbranhs;

        $dakmsm = clearaccounting::create(['accountingdates' => $cvdates,'clbranchs' => $brnahcs,'cvruclearence' => 1]);


        return redirect()->back()->with('success','Cvru Accounting Clear!!');

    }
     public function bankaccountinfcleareence(Request $request)
    {
        $baskdates = $request->banksdates;
        $brnchsda = $request->brnahchsdatas;

        $dakmsm = clearaccounting::create(['accountingdates' => $baskdates,'clbranchs' => $brnchsda,'bankclearence' => 1]);


        return redirect()->back()->with('success','Bank Accounting Clear!!');

    }

    public function onlinepaymentclearences(Request $request)
    {
        $odatess = $request->onlinedates;
        $obbranchs = $request->obranchs;

        $dakmsm = clearaccounting::create(['accountingdates' => $odatess,'clbranchs' => $obbranchs,'onlinepaymentsclearence' => 1]);


        return redirect()->back()->with('success','Online Payment Accounting Clear!!');

    }

   public function index()
    {
        
       $currentMonth = date('m');
         
         $paysdata = \DB::table('payments')
          ->leftjoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftjoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftjoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->select('re_admissions.*','payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');
                                                                 
        
                                                            })->whereMonth('payments.paymentdate', $currentMonth)->orderBy('payments.id','DESC')->get(); 
          $almarkusrt = User::where('usercategory','Marketing')->get();
          $branchalldata = Branch::all();

        return view('superadmin.accounting.collectionreport',compact('paysdata','almarkusrt','branchalldata'));

            
    }


   
    public function cvrudetails($datesf,$barnchs)
    {
        $currentMonth = date('m');

              $paymentsofbit =  \DB::table('admissionprocesses')
          ->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->whereDate('payments.paymentdate', '=', $datesf)
          ->where('admissionprocesses.suniversities','!=','BIT')
           ->where('payments.branchs', '=', $barnchs)
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')->orderBy('payments.id','DESC')->get(); 


          $rerpaymentsofbit =  \DB::table('re_admissions')
                            ->join('payments', 'payments.reinviceid', '=', 're_admissions.id')
                            ->whereDate('payments.paymentdate', '=', $datesf)
                            ->where('re_admissions.rsuniversities','!=','BIT')
                             ->where('payments.branchs', '=', $barnchs)
                            ->select('payments.*','re_admissions.*','payments.id as ppid','re_admissions.id as reid')->orderBy('payments.id','DESC')
                            ->get();

         return view('superadmin.accounting.cvrufeesdetails',compact('paymentsofbit','datesf','rerpaymentsofbit','barnchs'));
    }

    public function transferfees($id,$barnchs)
    {
            
            $ppasy = payment::find($id);

          $paymentsofbit =  \DB::table('admissionprocesses')
          ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->where('admissionprocesses.suniversities','!=','BIT')
          ->where('payments.id',$id)
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')->orderBy('payments.id','DESC')->first(); 
          //dd()


           $rerpaymentsofbit =  \DB::table('re_admissions')
                            ->join('payments', 'payments.reinviceid', '=', 're_admissions.id')
                              ->where('payments.id',$id)
                            ->where('re_admissions.rsuniversities','!=','BIT')
                            ->select('payments.*','re_admissions.*','payments.id as ppid','re_admissions.id as reid')->orderBy('payments.id','DESC')
                            ->first();

          return view('superadmin.accounting.transferfees',compact('paymentsofbit','id','ppasy','rerpaymentsofbit','barnchs'));
    } 

    public function updatedcvrutransferfees($id,$barnchs,Request $request)
    {
      $currendate = date('Y-m-d');
     
      $CvruFeesmodel = new CvruFees();
        $CvruFees = $CvruFeesmodel->create([

            'studentsnames'=> $request->studentsnames,
            'coursenames'=> $request->coursenames,
            'admissionsfors'=> $request->admissionfors,
            'totalfees'=> $request->totaalsfees,
            'tbalancefees'=> $request->blacncessfees,
            'payablefees'=> $request->payabdlefees,
            'universityfees'=> $request->univefess,
            'releeaseddates'=> $currendate,
            'preceiptnos'=> $request->receiptesnos,
            'studentsadmissionid'=> $request->admissionsid,
            'studentsreadmissionid'=> $request->readmissionsid,
            'studentid'=> $id,
            'cpaymentdate'=> $request->paymentsdate,
            'sverno'=> $request->studenterono,
            'cvrufees'=> $request->cvrufees,
            'bitfees'=> $request->bitfees,
        ]);

        return redirect('/cvrufees-details/'.$request->paymentsdate.'/'.$barnchs)->with('success','Fees Transfer Successfully');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $today = date('Y-m-d');

      // dd($today);

        $uBranchs = Auth::user()->branchs;

        $period = CarbonPeriod::create(date('Y').'-01-03', $today);
            $datesd = $period->toArray();
        
       
         
         $paysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.clearamountdate', '=', 'payments.paymentdate')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')
          ->where('payments.branchs',$uBranchs)
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->groupBy('payments.paymentdate')->orderBy('payments.paymentdate','DESC')->get(); 

        $branall = Branch::all();

                                                        //dd($paysdata);
        return view('superadmin.accounting.accounting',compact('paysdata','datesd','branall'));
    }


    public function filteraccounting(Request $request)
    {

        $branchs = $request->brnahcsdatas;

        if($branchs == 'BITSJ')
        {

                          $today = date('Y-m-d');

                    $period = CarbonPeriod::create(date('Y').'-01-03', $today);
                        $datesd = $period->toArray();

                     $paysdata = \DB::table('payments')
                      ->leftJoin('accountings', 'accountings.clearamountdate', '=', 'payments.paymentdate')
                      ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')
                      ->where('payments.branchs',$branchs)
                                                                    ->whereNotExists( function ($query) {
                                                                    $query->select(DB::raw(1))
                                                                            ->from('payments')
                                                                            ->whereRaw('accountings.paymentids = payments.id');  
                    
                                                                        })->groupBy('payments.paymentdate')->orderBy('payments.paymentdate','DESC')->get(); 

                    $branall = Branch::all();

                    return view('superadmin.accounting.filteraccountingallbranchs',compact('paysdata','datesd','branall','branchs'));

        }

        else
        {
                       $today = date('Y-m-d');

                    $period = CarbonPeriod::create(date('Y').'-06-01', $today);
                        $datesd = $period->toArray();

                     $paysdata = \DB::table('payments')
                      ->leftJoin('accountings', 'accountings.clearamountdate', '=', 'payments.paymentdate')
                      ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')
                      ->where('payments.branchs',$branchs)
                                                                    ->whereNotExists( function ($query) {
                                                                    $query->select(DB::raw(1))
                                                                            ->from('payments')
                                                                            ->whereRaw('accountings.paymentids = payments.id');  
                    
                                                                        })->groupBy('payments.paymentdate')->orderBy('payments.paymentdate','DESC')->get(); 

                    $branall = Branch::all();

                    return view('superadmin.accounting.filteraccountingallbranchs',compact('paysdata','datesd','branall','branchs')); 
        }

        

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store($da,$barnchs,Request $request)
    {
      
            $cvruadmissions =  \DB::table('payments')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->where('payments.branchs', '=', $barnchs)
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('admissionprocesses.suniversities','!=','BIT')
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')->orderBy('payments.id','DESC')->get(); 
         
          
        $clearpaysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('payments.branchs', '=', $barnchs)
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')->orderBy('payments.id','DESC')->get(); 

          $npaysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('payments.branchs', '=', $barnchs)
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')

                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get();


             $cashpaydata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('payments.paymentmode','Cash')
          ->where('payments.branchs', '=', $barnchs)
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')

                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get();
                                                            
            $cac = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('payments.revisedpaymentsmodes','Cheque Against Cash')
          ->where('payments.branchs', '=', $barnchs)
          ->select('payments.*','accountings.*','re_admissions.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
          ->orderBy('payments.id','DESC')->get();

        $otransferpaydata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->where('payments.branchs', '=', $barnchs)
          ->select('payments.*','accountings.*','re_admissions.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')

                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get();
        
        
        $nnotransferpaydata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
           ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->where('payments.branchs', '=', $barnchs)
          ->select('payments.*','accountings.*','re_admissions.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
            ->orderBy('payments.id','DESC')->get();

        $bankpaydata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $da)
          ->where('payments.paymentmode','=','Bank (Cheque)')
          ->where('payments.branchs', '=', $barnchs)
          ->select('payments.*','accountings.*','re_admissions.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')

                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get(); 
      
      $cashepxs = CashExpense::whereDate('exppaymendate',$da)->where('ebranchs', '=', $barnchs)->get();

           //$cashepxs = payment::join('payments','payments.id','=','cash_expenses.')                                             

                                                        //dd($paysdata); 

                                                        //dd($paysdata);
        return view('superadmin.accounting.makeaccounting',compact('da','npaysdata','otransferpaydata','bankpaydata','cashepxs','cashpaydata','clearpaysdata','nnotransferpaydata','cvruadmissions','cac','barnchs'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($dat,$barnchs)
    {
        
        $cvruadmissions =  \DB::table('payments')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
            ->where('payments.branchs', '=', $barnchs)
          ->whereDate('payments.paymentdate', '=', $dat)
          ->where('admissionprocesses.suniversities','!=','BIT')
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid','re_admissions.*')->orderBy('payments.id','DESC')->get();
          
          
        $today = date('Y-m-d');
         $cour = course::all();
       $sourcedata = Source::get();
        $folss = followup::get();
        $userdata = User::all();
        
         $accountincomes = payment::whereDate('paymentdate', '=',$dat)->where('branchs',$barnchs)->selectRaw('*, sum(paymentreceived) as paymentreceived')->get();
         
          $paymentsdata = \DB::table('payments')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
              ->where('payments.branchs', '=', $barnchs)
          ->whereDate('payments.paymentdate', '=', $dat)
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.*','re_admissions.id as reid')->orderBy('payments.id','DESC')->get();
          
          
          
             $clearpaysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
              ->where('payments.branchs', '=', $barnchs)
          ->whereDate('payments.paymentdate', '=', $dat)
         ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid','re_admissions.*')->orderBy('payments.id','DESC')->get(); 

         
        
         
          $paysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
              ->where('payments.branchs', '=', $barnchs)
          ->whereDate('payments.paymentdate', '=', $dat)
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','re_admissions.*','re_admissions.id as reid')

                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get();
         


       $cashpaydata = \DB::table('payments')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $dat)
            ->where('payments.branchs', '=', $barnchs)
          ->where('payments.paymentmode','=','Cash')
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','re_admissions.id as reid','re_admissions.*')->orderBy('payments.id','DESC')->get();
          
         $cac = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $dat)
            ->where('payments.branchs', '=', $barnchs)
          ->where('payments.revisedpaymentsmodes','Cheque Against Cash')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.*','re_admissions.id as reid')
          ->orderBy('payments.id','DESC')->get();
          /*    $cashpaydata = \DB::table('payments')->whereDate('paymentdate', '=', $dat)->where('paymentmode','=','Cash')->orWhere('paymentmode','Cheque Against Cash')->orderBy('id','DESC')->get();*/
                                                        

        $otransferpaydata = \DB::table('payments')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $dat)
            ->where('payments.branchs', '=', $barnchs)
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','re_admissions.id as reid','re_admissions.*')->orderBy('payments.id','DESC')->get(); 

        $bankpaydata = \DB::table('payments')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->whereDate('payments.paymentdate', '=', $dat)
            ->where('payments.branchs', '=', $barnchs)
          ->where('payments.paymentmode','=','Bank (Cheque)')
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','re_admissions.*','re_admissions.id as reid')->orderBy('payments.id','DESC')->get();

        $cashepxs = CashExpense::WhereDate('exppaymendate',$dat)->where('ebranchs', '=', $barnchs)->get();

                                                        //dd($paysdata);
                                                        
                                                        
                                                
        return view('superadmin.accounting.accountdetails',compact('cour','sourcedata','folss','userdata','paysdata','dat','cashpaydata','otransferpaydata','bankpaydata','cashepxs','accountincomes','paymentsdata','clearpaysdata','cvruadmissions','cac','barnchs'));
    }



    public function onlinepayment($dats)
    {
        $pmodes = PaymentSource::all();
        
      

        $paysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->whereDate('payments.paymentdate', '=', $dats)
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->select('payments.*','accountings.*','payments.id as ppid')
          ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get(); 
                                                            
          $clearpaysdata = \DB::table('payments')
          ->join('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->whereDate('payments.paymentdate', '=', $dats)
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')
          ->orderBy('payments.id','DESC')->get();
        

        return view('superadmin.accounting.onlinepaymentdetails',compact('pmodes','paysdata','clearpaysdata'));
    }

    public function onlinetransfer()
    {
        $paysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->select(DB::raw('SUM(payments.paymentreceived) As paymentreceived'),'payments.*','accountings.*','payments.id as ppid')
          ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->groupBy('payments.paymentdate')->orderBy('payments.id','DESC')->get(); 
        $clearpaysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
           ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')
          ->groupBy('payments.paymentdate')
          ->orderBy('payments.id','DESC')->get();

          return view('superadmin.accounting.onlinepaymentdetails',compact('paysdata','clearpaysdata'));
    }

    /*public function onlineaccountdetaills($datd)
    {
            $paysdata = \DB::table('payments')
          ->Join('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->Join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->whereDate('payments.paymentdate',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','payments.id as ppid','admissionprocesses.*','admissionprocesses.id as admid','re_admissions.id as reid')
          ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');
                                                                
        
                                                            })->orderBy('payments.id','DESC')->get();  
            $revidespaysdata = \DB::table('payments')
          ->Join('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->Join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->where('payments.revisedpaymentsmodes','!=','Cash')
          ->where('payments.revisedpaymentsmodes','!=','Cheque Against Cash')
          ->where('payments.revisedpaymentsmodes','!=','Bank (Cheque)')
          ->whereDate('payments.paymentdate',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','payments.id as ppid','admissionprocesses.*','admissionprocesses.id as admid','re_admissions.id as reid')
          ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');
                                                                
        
                                                            })->orderBy('payments.id','DESC')->get();
       
                                                            
                                                            
                                                            
        $clearpaysdata = \DB::table('payments')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
           ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->whereDate('payments.paymentdate',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
          ->groupBy('payments.receiptno')
          ->orderBy('payments.id','DESC')->get();
          
          
          $reclearpaysdata = \DB::table('payments')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
           ->where('payments.revisedpaymentsmodes','!=','Cash')
          ->where('payments.revisedpaymentsmodes','!=','Cheque Against Cash')
          ->where('payments.revisedpaymentsmodes','!=','Bank (Cheque)')
          ->whereDate('payments.paymentdate',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
          ->groupBy('payments.receiptno')
          ->orderBy('payments.id','DESC')->get();
         // dd($clearpaysdata);
         
    
          

          return view('superadmin.accounting.olinetranscaction',compact('paysdata','clearpaysdata','datd','reclearpaysdata','revidespaysdata'));
    }*/
    
     public function onlineaccountdetaills($datd,$barnchs)
    {
        $paysdata = \DB::table('payments')
          ->Join('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->Join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->where('payments.branchs',$barnchs)
          ->whereDate('payments.paymentdate',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','payments.id as ppid','admissionprocesses.*','admissionprocesses.id as admid','re_admissions.id as reid')
          ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get();
                                                            //dd($paysdata);


         $revispaymodspaysdata = \DB::table('payments')
          ->Join('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->Join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->where('payments.revisedpaymentsmodes','!=','Cash')
          ->where('payments.revisedpaymentsmodes','!=','Cheque Against Cash')
          ->where('payments.revisedpaymentsmodes','!=','Bank (Cheque)')
          ->where('payments.branchs',$barnchs)
          ->whereDate('payments.rdatess',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','payments.id as ppid','admissionprocesses.*','admissionprocesses.id as admid','re_admissions.id as reid')
          ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('accountings.paymentids = payments.id');  
        
                                                            })->orderBy('payments.id','DESC')->get(); 
                

              $revisedpaysdata = \DB::table('payments')
          ->Join('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->Join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->Join('cheque_against_money', 'cheque_against_money.cacpid', '=', 'payments.id')
         ->where('cheque_against_money.cacpaymodes','!=','Cash')
          ->where('cheque_against_money.cacpaymodes','!=','Cheque Against Cash')
          ->where('cheque_against_money.cacpaymodes','!=','Bank (Cheque)')
          ->where('payments.branchs',$barnchs)
          ->whereDate('cheque_against_money.cacpaymentdates',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','cheque_against_money.*','payments.id as ppid','admissionprocesses.*','admissionprocesses.id as admid','re_admissions.id as reid')
          ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('cheque_against_money')
                                                                ->whereRaw('accountings.paymentids = cheque_against_money.cacpid');  
        
                                                            })->orderBy('payments.id','DESC')->get(); 
                                                            
               

        $clearpaysdata = \DB::table('payments')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
           ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->where('payments.branchs',$barnchs)
          ->whereDate('payments.paymentdate',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
          ->groupBy('payments.receiptno')
          ->orderBy('payments.id','DESC')->get();
          
           //dd($clearpaysdata);


           $revisedclearpaysdata = \DB::table('payments')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
           ->where('payments.revisedpaymentsmodes','!=','Cash')
          ->where('payments.revisedpaymentsmodes','!=','Cheque Against Cash')
          ->where('payments.revisedpaymentsmodes','!=','Bank (Cheque)')
          ->where('payments.branchs',$barnchs)
          ->whereDate('payments.rdatess',$datd)
          ->select('re_admissions.*','payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
          ->groupBy('payments.receiptno')
          ->orderBy('payments.id','DESC')->get();



          $cacclearpaysdata = \DB::table('payments')
          ->leftJoin('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('cheque_against_money', 'cheque_against_money.cacpid', '=', 'payments.id')
           ->where('cheque_against_money.cacpaymodes','!=','Cash')
          ->where('cheque_against_money.cacpaymodes','!=','Cheque Against Cash')
          ->where('cheque_against_money.cacpaymodes','!=','Bank (Cheque)')
          ->where('payments.branchs',$barnchs)
          ->whereDate('cheque_against_money.cacpaymentdates',$datd)
          ->select('re_admissions.*','payments.*','cheque_against_money.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.id as reid')
          ->groupBy('payments.receiptno')
          ->orderBy('payments.id','DESC')->get();
         // dd($clearpaysdata);
         
    
          

          return view('superadmin.accounting.olinetranscaction',compact('paysdata','clearpaysdata','datd','revisedpaysdata','cacclearpaysdata','revispaymodspaysdata','revisedclearpaysdata','barnchs'));
    }


    public function clearonlinepaymenrts($changeid)
    {   
        //$getid = leadsfollowups::where('leadsfrom',$datas7)->update(array('fstatus' => 1)); 
        $uodat = payment::find($changeid);
         $Accountingmodel = new Accounting();
        $Accounting = $Accountingmodel->create([
            'paymentids'=> $changeid,
            'ppcollections'=> $uodat->paymentreceived,
            'ppaymentmodes'=> $uodat->paymentmode,
            'clearstatus'=> 1,
        ]);

        return response()->json(
                    [
                        'success' => true,
                        'message' => 'Payment Clear Successfully!!'
                    ]
                );  

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      public function cashexpense($das,$barnchs)

   {
            $ex = ExpenseCategory::all();

        $cashpaydata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
         ->leftjoin('re_admissions','re_admissions.id','=','payments.reinviceid')
           ->whereDate('payments.paymentdate', '=', $das)
           ->where('payments.branchs', '=', $barnchs)
          ->where('payments.paymentmode','Cash')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.*','re_admissions.id as reid')
          ->orderBy('payments.id','DESC')->get();
          
          $cac = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
           ->whereDate('payments.rdatess',$das)
           ->where('payments.branchs', '=', $barnchs)
          ->where('payments.revisedpaymentsmodes','Cheque Against Cash')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')
          ->orderBy('payments.id','DESC')->get();
    
            $carryforwardamount = Accounting::where('abranchs',$barnchs)->whereDate('carrynextdatews',$das)->get();
    
         $reviseddatas = \DB::table('payments')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->leftJoin('re_admissions','re_admissions.id','=','payments.reinviceid')
          ->whereDate('payments.rdatess',$das)
          ->where('payments.branchs', '=', $barnchs)
          ->Where('payments.revisedpaymentsmodes','Cheque Against Cash')
          ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid','re_admissions.*','re_admissions.id as reid')
          ->orderBy('payments.id','DESC')->get();



          $cheqacashdetai = \DB::table('payments')
          ->leftJoin('cheque_against_money','cheque_against_money.cacpid','=','payments.id')
          ->whereDate('cheque_against_money.cacpaymentdates',$das)
          ->where('payments.branchs', '=', $barnchs)
          ->where('cheque_against_money.cacpaymodes','Cheque Against Cash')
          ->select('payments.*','cheque_against_money.*','payments.id as ppid')
          ->orderBy('payments.id','DESC')->get();

    //dd($reviseddatas);
          //  dd($barnchs);

         $newdata = CashExpense::where('exppaymendate',$das)->where('ebranchs', '=', $barnchs)->get();

         $cashepxs = CashExpense::all();


        return view('superadmin.accounting.cashexpense',compact('cashpaydata','newdata','ex','das','cashepxs','carryforwardamount','cac','reviseddatas','cheqacashdetai','barnchs'));
    }

     public function storeexpensedatas(Request $request)
    {   

        // dd($request->usersId);
            $usid = $request->usersId;
            $sala =  $request->copyvalue;
            $expensfor = $request->expefor;
            $expbranchs = $request->cabranchs;

            /*for($i=0; $i < (count($usid)); $i++)
            {

                     if($usid[$i] != null && str_contains($expensfor[$i], 'Salary'))
                        {
                            //dd("exist!!!");

                             $datas = explode('-',$request->datesofex);

                              $scalutions = SalaryCalculations::whereMonth('datesofsalarys',$datas[1])->whereYear('datesofsalarys',$datas[0])->where('user_details_id',$usid[$i])->latest()->first();
                              $deductedamounts = User_Salary_Deductions::where('salssalarysid',$scalutions->id)->latest()->first();

                                $pendingsala = $deductedamounts->salspendingsalarys;
                                $salarys = $sala[$i];
                                    $finasalal = $pendingsala - $salarys;
                              
                                    //dd($finasalal);


                              $CashExpense = new User_Salary_Deductions([
                                                   
                                                    'salssalarysid'   => $scalutions->id,
                                                    'salsusersid'   => $scalutions->user_details_id,
                                                    'salsworkingsalarys'   => $scalutions->users_salarys,
                                                    'salsfinalsalarys'   => $scalutions->uwrkingsalary,
                                                    'totalrealeasesalary'   => $deductedamounts->salspaidsalarys,
                                                    'salspaidsalarys'   => $salarys,
                                                    'salspendingsalarys'   => $finasalal,
                                                    'salspaymentdate'   => $request->datesofex,
                                                    'salspaymoddes'   => "Cash",
                                                    'smonthsdatas'   => $deductedamounts->smonthsdatas,
                                                    
                                                    
                                                ]);
                                                $CashExpense->save();

                                //dd($scalutions);
                        }


                        if($usid[$i] != null && str_contains($expensfor[$i], 'Incentive'))
                        {


                                        $getUsersCatgory = User::find($usid[$i]);

                                        if($getUsersCatgory->usercategory == 'Marketing')
                                        {

                                                                              $months = explode('-',$request->datesofex);

                                                                        $ernrollmentfees = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->where("payments.studenterno","!=",null)->where('admissionprocesses.admissionsusersid',$usid[$i])->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->get();

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

                                                                 
                                                                
                                                                
                                                                $installmentfees = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->where("payments.studenterno",null)->where('admissionprocesses.admissionsusersid',$usid[$i])->whereMonth('payments.paymentdate',$months[1])->whereYear('payments.paymentdate',$months[0])->get();

                                                              

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
                                                                          ->where('admissionprocesses.admissionsusersid',$usid[$i])
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

                                                                  $remaincent = $totalicen - $sala[$i];


                                                                  $IncentiveReleasePaymentsmodel = new IncentiveReleasePayments();
                                                                $IncentiveReleasePayments = $IncentiveReleasePaymentsmodel->create([
                                                                    'incentcollections'=> $val,
                                                                    'mincentivs'=> $insentives.'%',
                                                                    'payableincentivespayments'=> $sala[$i],
                                                                    'remainingincentives'=> $remaincent,
                                                                    'incpaymentsmodes'=> 'Cash',
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


                                                                  $remaincent = $totalicen - $sala[$i];


                                                                  $IncentiveReleasePaymentsmodel = new IncentiveReleasePayments();
                                                                $IncentiveReleasePayments = $IncentiveReleasePaymentsmodel->create([
                                                                    'incentcollections'=> $val,
                                                                    'mincentivs'=> $insentives.'%',
                                                                    'payableincentivespayments'=> $sala[$i],
                                                                    'remainingincentives'=> $remaincent,
                                                                    'incpaymentsmodes'=> 'Cash',
                                                                    'incentivespaymentsdates'=> $request->datesofex,
                                                                    'ibranchs'=> $getUsersCatgory->branchs,
                                                                    'mothsof'=> $request->datesofex,
                                                                ]);


                                        }


                        }





            }
*/

       

           
        //dd($request->usersId);
                $expensfor = $request->expefor;
                $expamountd = $request->expenseamounts;
                $expdats = $request->expensedates;
                $dars = $request->datesofex;
                $usersdata = $request->usersId;
                 $casremarks = $request->cashexpenseremarks;



               

              //   dd($expdats,$usersdata);
                // dd();

           for($i=0; $i < (count($expensfor)); $i++)
                        {
                                   $CashExpense = new CashExpense([
                                   
                                    'expnsenewamounts'   => $expamountd[$i],
                                    'expensefor'   => $expensfor[$i],
                                    'cusersids'   => $usersdata[$i],
                                    'ebranchs'   => $expbranchs,
                                    'expenseremarks'   => $casremarks[$i],
                                    'exppaymendate'   => $expdats[$i],
                                    
                                    
                                ]);
                                $CashExpense->save();
                        } 

                          return redirect()->back()->with('success','Cash Expense Add Successfully!!');

    }

    public function expensecollections()
    {
        $userBrnachs = Auth::user()->branchs;

        $branall = Branch::all();

        $excollec = payment::where('branchs',$userBrnachs)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->groupBy('paymentdate')->orderBy('id','DESC')->get();

         $cashepxs = CashExpense::where('ebranchs',$userBrnachs)->groupBy('exppaymendate')->get();

          return view('superadmin.accounting.totalexpense',compact('excollec','cashepxs','userBrnachs','branall'));

    }


   public function accountincome()
    {

        $userBrnachs = Auth::user()->branchs;
            $accountincomes = payment::where('branchs',$userBrnachs)->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->orderBy('id','DESC')->get();



            $excollec = payment::where('branchs',$userBrnachs)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->orderBy('id','DESC')->get();
            
             $today = date('Y-m-d');

            $period = CarbonPeriod::create(date('Y').'-01-03', $today);
            $dated = $period->toArray();

             $datesd = array_reverse($dated);

             $branall = Branch::all();

            return view('superadmin.accounting.income',compact('accountincomes','excollec','datesd','userBrnachs','branall'));
    }



    public function cashaccounting()
    {
           // $paymentcollections = payment::groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->get();


            $accountincomes = payment::where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->orderBy('id','DESC')->get();

                $cashepxs = CashExpense::groupBy('exppaymendate')->get();

            //$excollec = payment::where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->get();

            return view('superadmin.accounting.cashaccounting',compact('accountincomes','cashepxs'));
    }

     public function bankdetails()
    {
            $getreminderdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid',DB::raw('sum(paymentreceived) as paymentreceived'))
         ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
        ->where('payments.paymentmode', '=', 'Bank (Cheque)')
         ->groupBy('payments.paymentdate')
         ->orderBy('payments.id','DESC')
         ->get();

            return view('superadmin.accounting.bankdetails',compact('getreminderdata'));
    }

     public function accountbankdetails($bdas,$barnchs)
    {
            $getreminderdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
         ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
         ->whereDate('payments.paymentdate',$bdas)
         ->whereDate('payments.branchs',$barnchs)
         ->where('payments.paymentmode', '=', 'Bank (Cheque)')
         ->orderBy('payments.chequedate','DESC')
         ->get();
         
         $rereminderdata = payment::select('re_admissions.*','payments.*','re_admissions.id as reid','payments.id as pid')
         ->Join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
         ->whereDate('payments.paymentdate',$bdas)
         ->whereDate('payments.branchs',$barnchs)
         ->where('payments.paymentmode', '=', 'Bank (Cheque)')
         ->orderBy('payments.chequedate','DESC')
         ->get();
         
         

            return view('superadmin.accounting.bankdatewsieaccount',compact('getreminderdata','bdas','rereminderdata','barnchs'));
         
         

            //return view('superadmin.accounting.bankdatewsieaccount',compact('getreminderdata','bdas'));
    }

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
    public function update(Request $request)
    {
        
        $admissionsId = $request->admissionid;

        $data= array();
      
        $result = admissionprocesscourses::select('courses.*','admissionprocesscourses.*','payments.*',DB::raw('sum(paymentreceived) as paymentreceived'))->leftJoin('payments','payments.inviceid','=','admissionprocesscourses.invid')->leftjoin('courses','courses.id','=','admissionprocesscourses.univecoursid')->where('admissionprocesscourses.invid',$admissionsId)->get();

              foreach($result as $res)
              {
                  $row = array();
                  $row[] = $res->coursename;
                  $row[] = $res->universitiesfees;
                  $row[] = $res->universitiesfees - $res->paymentreceived ;
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy(Request $request)
    {
          $dattre = $request->caaryforwardsdates;
           $newdates = date('Y-m-d',strtotime($dattre.'+1 day'));

          //dd($newdates);

         $Accountingmodel = new Accounting();
        $Accounting = $Accountingmodel->create([
            'caarryforwardamount'=> $request->caarryforwardamount,
            'carrynextdatews'=> $newdates,
            'abranchs'=> $request->aabranchs,
            'carryforolddates'=> $dattre,
            'ostatus'=> 1,
        ]);

            return redirect()->back()->with('success','Your Amount of ₹'.$request->caarryforwardamount.'is Carry Forwarded Date of'.$newdates);

    }
    
    public function updatenewcashexpense($updatenewcaryyforwarddat,Request $request)
    {
          //$dattre = $request->caaryforwardsdates;
           $newdates = date('Y-m-d',strtotime($updatenewcaryyforwarddat.'+1 day'));

          //dd($newdates);

        /* $Accountingmodel = new Accounting();
        $Accounting = $Accountingmodel->create([
            'caarryforwardamount'=> $request->caarryforwardamount,
            'carrynextdatews'=> $newdates,
            'carryforolddates'=> $dattre,
            'ostatus'=> 1,
        ]);*/

        $updates = Accounting::whereDate('carryforolddates',$updatenewcaryyforwarddat)->first();
        $updates->caarryforwardamount = $request->caarryforwardamount;
         $updates->ostatus = 1;
         $updates->save();


            return redirect('/makea-accounting/'.$updatenewcaryyforwarddat.'/'.$updates->abranchs)->with('success','Your Amount of ₹'.$updatenewcaryyforwarddat.'is Carry Forwarded Date of'.$newdates);

    }
    
    public function deletedata($id)
    {
        $dele = CashExpense::find($id);
        $dele->delete();

        return redirect()->back()->with('Success','Expense Delted Successfully!');

    }
    
    public function cashexpensedetails($expdats)
    {
            $casheexpdet = CashExpense::whereDate('exppaymendate',$expdats)->get();

            return view('superadmin.accounting.expensesdetails',compact('casheexpdet','expdats'));
    }
    
    public function allclear(Request $request)
    {
        $Accountingmodel = new Accounting();
        $Accounting = $Accountingmodel->create([
            'clearamountdate'=> $request->amountcleardates,
            'clrstatus'=> 1,
            
        ]);

        return redirect()->back()->with('success','All Amounts Clear Successfully !!');
    }
    
    public function resetchequedetails($id)
    {
        $getdata = payment::find($id);

            if($getdata->chequedepositsto != null)
            {
                    $getdata->chequestatus = "0";
                    $getdata->chequedepositsto = "NULL";
                    $getdata->save();

            }

            else
            {

                     $getdata->chequestatus = "0";
                     $getdata->save();
                    $getchequedele = ChequeAgainstMoney::where('cacpid',$getdata->id)->get();
                    $getchequedele->each->delete();

            }

            return redirect('/bank-accounting-details/'.$getdata->paymentdate.'/'.$getdata->branchs);
    }
    
    public function accountsummary()
    {
        $currentmonths = date('m');
        $usersbranchs = Auth::user()->branchs;
        $branall = Branch::all();

        $getalldatas = payment::where('branchs',$usersbranchs)->whereMonth('paymentdate',$currentmonths)->orderBy('id','DESC')->get();

        return view('superadmin.accounting.accountsummarys',compact('getalldatas','branall'));

    }

    public function filteraccountsummary(Request $request)
    {
        $userbranchs = $request->allbranchs;
        $amonths = $request->allmonthsdata;
        $branall = Branch::all();

        $months = explode('-',$amonths);

         $getalldatas = payment::where('branchs',$userbranchs)->whereMonth('paymentdate',$months[1])->whereYear('paymentdate', '=', $months[0])->orderBy('id','DESC')->get();

        return view('superadmin.accounting.filteraccountsummarys',compact('getalldatas','branall','amonths','userbranchs'));

    }
}
