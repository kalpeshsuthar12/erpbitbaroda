<?php

namespace App\Http\Controllers;

use App\course;
use App\Source;
use App\followup;
use App\User;
use App\Accounting;
use App\payment;
use App\PaymentSource;
use App\ExpenseCategory;
use App\CashExpense;
use App\Branch;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;

class FilterAccountingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {
            $months = $request->MonthsFilters;

            $getbranch  = $request->branchData; 
            $musers = $request->markertuswer;

           $monts = explode('-',$months);
           $monhsts = $monts[1];
           $yeaes = $monts[0];
           //dd($monhsts);
           //dd($yeaes);
           if($getbranch)
           {     $mpaysdatas = "";
                 $brnahspaysdata = \DB::table('payments')
                      ->Join('accountings', 'accountings.paymentids', '=', 'payments.id')
                      ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->whereMonth('payments.paymentdate',$monhsts)
                     ->whereYear('payments.paymentdate', '=', $yeaes)
                     ->where('admissionprocesses.stobranches',$getbranch)
                      ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')
                      ->orderBy('payments.id','DESC')->get(); 

                                                                    //dd($paysdata);
                     $almarkusrt = User::where('usercategory','Marketing')->get();
                    $branchalldata = Branch::all();

                    return view('superadmin.accounting.filteraccounting',compact('monhsts','yeaes','brnahspaysdata','mpaysdatas','almarkusrt','branchalldata','musers','getbranch'));
           }

           elseif($musers)
           {
                $brnahspaysdata = "";
                 $mpaysdatas = \DB::table('payments')
                      ->Join('accountings', 'accountings.paymentids', '=', 'payments.id')
                      ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->whereMonth('payments.paymentdate',$monhsts)
                     ->whereYear('payments.paymentdate', '=', $yeaes)
                     ->where('admissionprocesses.admissionsusersid',$musers)
                      ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')
                      ->orderBy('payments.id','DESC')->get(); 


                       $almarkusrt = User::where('usercategory','Marketing')->get();
                        $branchalldata = Branch::all();

                                                                    //dd($paysdata);
                    return view('superadmin.accounting.filteraccounting',compact('monhsts','yeaes','mpaysdatas','brnahspaysdata','branchalldata','almarkusrt','getbranch','musers'));
           }
                        




      
    }

    /*{
            $months = $request->MonthsFilters;

            $getbranch  = $request->branchData; 
            $musers = $request->markertuswer;

           $monts = explode('-',$months);
           $monhsts = $monts[1];
           $yeaes = $monts[0];
           //dd($monhsts);
           //dd($yeaes);
           if($getbranch)
           {     $mpaysdatas = "";
                 $brnahspaysdata = \DB::table('payments')
                      ->Join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
                      ->whereMonth('payments.paymentdate',$monhsts)
                     ->whereYear('payments.paymentdate', '=', $yeaes)
                     ->where('admissionprocesses.stobranches',$getbranch)
                      ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')
                      ->orderBy('payments.id','DESC')->get(); 

                                                                    //dd($paysdata);
                     $almarkusrt = User::where('usercategory','Marketing')->get();
                    $branchalldata = Branch::all();

                    return view('superadmin.accounting.filteraccounting',compact('monhsts','yeaes','brnahspaysdata','mpaysdatas','almarkusrt','branchalldata','musers','getbranch'));
           }

           elseif($musers)
           {
                $brnahspaysdata = "";
                 $mpaysdatas = \DB::table('payments')
                      ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
                      ->whereMonth('payments.paymentdate',$monhsts)
                     ->whereYear('payments.paymentdate', '=', $yeaes)
                     ->where('admissionprocesses.admissionsusersid',$musers)
                      ->select('payments.*','admissionprocesses.*','payments.id as ppid','admissionprocesses.id as admid')
                      ->orderBy('payments.id','DESC')->get(); 


                       $almarkusrt = User::where('usercategory','Marketing')->get();
                        $branchalldata = Branch::all();

                                                                    //dd($paysdata);
                    return view('superadmin.accounting.filteraccounting',compact('monhsts','yeaes','mpaysdatas','brnahspaysdata','branchalldata','almarkusrt','getbranch','musers'));
           }
                        




      
    }*/

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

         $months = $request->MonthsFilters;
         $brancha = $request->branchsdatas;

           $monts = explode('-',$months);

        //$excollec = payment::where('branchs',$brancha)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->whereMonth('paymentdate',$monts[1])->whereYear('paymentdate', '=', $monts[0])->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->get();


          $excollec = payment::where('branchs',$brancha)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->whereMonth('paymentdate',$monts[1])->whereYear('paymentdate', '=', $monts[0])->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->orderBy('id','DESC')->get();

         $cashepxs = CashExpense::where('ebranchs',$brancha)->whereMonth('exppaymendate',$monts[1])->whereYear('exppaymendate', '=', $monts[0])->groupBy('exppaymendate')->get();

          $branall = Branch::all();

          return view('superadmin.accounting.filtertotalexpense',compact('excollec','cashepxs','brancha','branall','months'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dates = $request->MonthsFilters;

           /*$monts = explode('-',$months);

        $accountincomes = payment::whereMonth('paymentdate',$monts[1])->whereYear('paymentdate', '=', $monts[0])->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->get();



            $excollec = payment::where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->whereMonth('paymentdate',$monts[1])->whereYear('paymentdate', '=', $monts[0])->groupBy('paymentdate')->get();*/

           // return view('superadmin.accounting.filterincome',compact('dates'));

          $months = $request->MonthsFilters;
         $brancha = $request->branchsdatas;

           $monts = explode('-',$months);

            $accountincomes = payment::where('branchs',$brancha)->whereMonth('paymentdate',$monts[1])->whereYear('paymentdate', '=', $monts[0])->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->orderBy('id','DESC')->get();



            $excollec = payment::where('branchs',$brancha)->whereMonth('paymentdate',$monts[1])->whereYear('paymentdate', '=', $monts[0])->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->orderBy('id','DESC')->get();
            

            if($brancha == "BITSJ")
            {
                 $today = date('Y-m-d');
                $period = CarbonPeriod::create($months.'-01', $today);
            $datesd = $period->toArray();

                     $branall = Branch::all();
                     $today = date('Y-m-d');
                    return view('superadmin.accounting.filterincome',compact('accountincomes','excollec','datesd','brancha','branall','months'));
            }

            else
            {
                 $today = date('Y-m-d');
                $period = CarbonPeriod::create($months.'-01', $today);
            $datesd = $period->toArray();

                     $branall = Branch::all();
                     $today = date('Y-m-d');
                    return view('superadmin.accounting.filterincome',compact('accountincomes','excollec','datesd','brancha','branall','months'));
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
        $months = $request->MonthsFilters;
        $brancha = $request->branchsdatas;

           $monts = explode('-',$months);
           //dd($months);

              $getreminderdata = payment::select(DB::raw('sum(paymentreceived) as paymentreceived'))
           ->where('branchs',$brancha)
            ->where('paymentmode', '=', 'Bank (Cheque)')
        ->whereMonth('chequedate',$monts[1])
        ->whereYear('chequedate', '=', $monts[0])
         ->orderBy('chequedate','DESC')
         ->get();

          $branall = Branch::all();

         return view('superadmin.accounting.filterbankdetails',compact('getreminderdata','months','brancha','branall'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $months = $request->MonthsFilters;
        $brancha = $request->branchsdatas;

           $monts = explode('-',$months);
        
        $paysdata = \DB::table('payments')
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->whereMonth('payments.paymentdate',$monts[1])
          ->whereYear('payments.paymentdate', '=', $monts[0])
          ->where('payments.branchs',$brancha)
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
          ->leftJoin('accountings', 'accountings.paymentids', '=', 'payments.id')
          ->leftJoin('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
            ->where('payments.branchs',$brancha)
          ->whereMonth('payments.paymentdate',$monts[1])
          ->whereYear('payments.paymentdate', '=', $monts[0])
          ->where('payments.paymentmode','!=','Cash')
          ->where('payments.paymentmode','!=','Cheque Against Cash')
          ->where('payments.paymentmode','!=','Bank (Cheque)')
          ->select('payments.*','accountings.*','admissionprocesses.*','payments.id as ppid')
          ->orderBy('payments.id','DESC')->get();

          $branall = Branch::all();
          return view('superadmin.accounting.filteronlinepaymentdetails',compact('paysdata','clearpaysdata','months','brancha','branall'));
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
          $months = $request->MonthsFilters;

           $monts = explode('-',$months);

        

        $accountincomes = payment::where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->whereMonth('paymentdate',$monts[1])
          ->whereYear('paymentdate', '=', $monts[0])->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->get();

                $cashepxs = CashExpense::whereMonth('exppaymendate',$monts[1])
          ->whereYear('exppaymendate', '=', $monts[0])->groupBy('exppaymendate')->get();

            //$excollec = payment::where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->get();

            return view('superadmin.accounting.filtercashaccounting',compact('accountincomes','cashepxs'));
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
