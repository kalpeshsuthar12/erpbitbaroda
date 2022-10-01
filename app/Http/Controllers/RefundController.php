<?php

namespace App\Http\Controllers;
use App\students;
use App\Refund;
use App\RefundsSettlements;
use App\Branch;
use App\payment;
use App\admissionprocess;
use App\PaymentSource;
use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $refundsdata =DB::table('refunds')
                ->join('branches', 'branches.id', '=', 'refunds.branchId')
                ->join('students', 'students.id', '=', 'refunds.studId')
                ->select('refunds.id','refunds.paymentmode', 'refunds.refundamount','refunds.created_at','branches.branchname','students.studentname')
                ->get();

            return view('superadmin.refunds.manage',compact('refundsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Branch $Branch)
    {
        $brans = Branch::get();
        $studes = students::get();

        return view('superadmin.refunds.create',compact('brans','studes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,payment $payment)
    {

        $studsid = $request->students;
        $refundwd = $request->rmount;
          $Refundmodel = new Refund();
        $Refund = $Refundmodel->create([
            'branchId'=> $request->brnaches,
            'studId'=> $request->students,
            'paymentmode'=> $request->pmode,
            'refundamount'=> $request->rmount,
        ]);

        $paymentssdat = payment::where('studentsid',$studsid)->pluck('paymentreceived');
          //  $prefundamount =  $paymentssdat - $refundwd;
       // dd($prefundamount);

            $ds = $payment->where('studentsid',$studsid)->decrement('paymentreceived', $refundwd);

           /// $ivd = $ds->id;
            return redirect('/paymentreceipt/'.$ds)->with('success','Refund Successfully Done!!!');
            //dd($ds);
        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function show(Refund $refund)
    {
        //
    }
    



    public function findstudents(Request $request)
    {
        $fbys = $request->filterby;

            if($fbys == 'Filter By Name')
            {   
                $filtermobiles = 0; 
                $fnames = $request->filterbynames;

                               // dd($fnames);
                                $filtername = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.studentname','like','%'.$fnames.'%')->groupBy('payments.inviceid')->get(); 

                       
                         foreach($filtername as $studentpaymen)
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

                        return view('superadmin.refunds.filtersstudents',compact('filtername','filtermobiles'));

            }

            else if($fbys == 'Filter By Mobile')
            {
                $filtername = 0;
                $fmobis = $request->filterbymobile;
               // $fnames = $request->filterbynames;

                               // dd($fnames);
                                $filtermobiles = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.sphone',$fmobis)->orWhere('admissionprocesses.swhatsappno',$fmobis)->groupBy('payments.inviceid')->get(); 

                       
                         foreach($filtermobiles as $studentpaymen)
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

                        return view('superadmin.refunds.filtersstudents',compact('filtername','filtermobiles'));
            }
    }


    public function refundprocess(Request $request)
    {
        $admissionsids = $request->getadmissionsids;

        $admissiondetails = admissionprocess::find($admissionsids);
        $payemntsreceiveds = payment::where('inviceid',$admissionsids)->get();
        $paymodes = PaymentSource::all();


        return view('superadmin.refunds.refundsprocess',compact('admissiondetails','payemntsreceiveds','paymodes','admissionsids'));
       // dd($admissionsids);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function completerefundprocess($id,Request $request)
    {

        $Refundmodel = new Refund();
        $Refund = $Refundmodel->create([
            'refuadmissionid'=> $id,
            'refunstudentanmmes'=> $request->studentsnamrs,
            'refunmobileno'=> $request->mobilenos,
            'refunemail'=> $request->emaisl,
            'refunenrollmentsno'=> $request->enrollmnentsnos,
            'refuntotalfees'=> $request->tfees,
            'refuntotalpaymentreceived'=> $request->tpaymentreceived,
            'refunrefundamounts'=> $request->refundsamounts,
            'refunremaiiningamounts'=> $request->remoaindinignamountds,
            'refunrefundates'=> $request->refunddates,
            'refunpaymodes'=> $request->paymentsmodes,
            'refunremarks'=> $request->refundsremarks,
        ]);

        $admissiondetails = admissionprocess::find($id);
        $admissiondetails->admissioncancelstauts = 1;
        $admissiondetails->save();

         return redirect('/cancel-admissions')->with('success','Refund Successfully Done!!!');
        
    }

    public function edit(Refund $refund)
    {
        $canceladmissions = Refund::join('admissionprocesses','admissionprocesses.id','=','refunds.refuadmissionid')->select('refunds.*','admissionprocesses.*','admissionprocesses.id as aid')->get();


        $intallments = admissionprocess::join('payments','payments.inviceid','=','admissionprocesses.id')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->get();

         $chequecancellat = admissionprocess::join('payments','payments.inviceid','=','admissionprocesses.id')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->get();

        return view('superadmin.refunds.canceladmission',compact('canceladmissions','intallments','chequecancellat'));
    }


    public function refundsettlements($id)
    {
        $refundsettlements = Refund::join('admissionprocesses','admissionprocesses.id','=','refunds.refuadmissionid')->select('refunds.*','admissionprocesses.*','admissionprocesses.id as aid','refunds.id as refid')->where('refunds.refuadmissionid',$id)->first();


        return view('superadmin.refunds.refundssettlementsforms',compact('refundsettlements'));
    }

    public function storerefundsettlements($id,Request $request)
    {
         /*$RefundsSettlementsmodel = new RefundsSettlements();
        $RefundsSettlements = $RefundsSettlementsmodel->create([
            'rspaymentsdate'=> $request->pdate,
            'rsstudentsnames'=> $request->snames,
            'rsenrollmentno'=> $request->senrollemts,
            'rscourse'=> $request->scourse,
            'rspayablefees'=> $request->payablefees,
            'rsrefundamounts'=> $request->ramounts,
            'rsbalance'=> $request->rbalance,
            'rscmonths'=> $request->cmonth,
            'rsbranchs'=> $request->rbranchs,
            'rsusers'=> $request->rusers,
            'rsstudentadmissionids'=> $request->aids,
            'rspaymentids'=> $request->pids,
           
        ]);*/

        $dates =  Carbon::createFromFormat('Y-m',$request->cmonth);

        $yuodate = Refund::find($id);

        $yuodate->refunstudentanmmes = $request->snames;
        $yuodate->refunenrollmentsno = $request->senrollemts;
        $yuodate->refcourses = $request->scourse;
        $yuodate->refuntotalpaymentreceived = $request->payablefees;
        $yuodate->resettlementsamounts = $request->ramounts;
        $yuodate->reseetlementsbalances = $request->rbalance;
        $yuodate->recollectionsmonths = $dates;
        $yuodate->rfromsbranchs = $request->rbranchs;
        $yuodate->rformsusers = $request->rusers;
        $yuodate->ressttlemenstspaymentsid = $request->pids;
        $yuodate->resettlementsadmissionsids = $request->aids;
        $yuodate->save();

        

      

        return redirect('/cancel-admissions')->with('success','Settlements created successfully!');
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Refund $refund)
    {   
         $userBranch = Auth::user()->branchs;
        $canceladmissions = Refund::join('admissionprocesses','admissionprocesses.id','=','refunds.refuadmissionid')->select('refunds.*','admissionprocesses.*','admissionprocesses.id as aid')->where('admissionprocesses.stobranches',$userBranch)->get();

        $intallments = admissionprocess::join('payments','payments.inviceid','=','admissionprocesses.id')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->get();


         $chequecancellat = admissionprocess::join('payments','payments.inviceid','=','admissionprocesses.id')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->get();


        return view('centremanager.admissionprocess.canceladmission',compact('canceladmissions','intallments','chequecancellat'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function destroy(Refund $refund)
    {
        //
    }
}
