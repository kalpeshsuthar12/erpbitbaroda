<?php

namespace App\Http\Controllers;
use App\leads;
use App\User;
use App\Branch;
use App\PaymentSource;
use App\IncentiveReleasePayments;
use App\PdcReleasePayments;
use App\payment;
use Illuminate\Http\Request;
use DB;

class IncentiveCalculationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $currentyear = date("Y");
        $currentmonth = date("m");
        $userdata = User::all();
        $branchdata = Branch::all();
       
        $paymr = payment::select("id",DB::raw("(DATE_FORMAT(paymentdate, '%Y-%m')) as month_year"))->orderBy('paymentdate')->groupBy(DB::raw("DATE_FORMAT(paymentdate, '%Y-%m')"))->get();

        //dd($paymr);
       
        return view('superadmin.incentivecalculation.manage',compact('userdata','branchdata','paymr'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $fbys = $request->filtesbys;
        
        
        

        if($fbys == 'By Users')
        {
            $userdata = User::all();
            $branchdata = Branch::all();
            $monthsdata = $request->ustartmonth;
            $fusers = $request->users;
            $getusers = User::select('name')->where('id',$fusers)->first();
            $fbranchs = "";

            $paymr = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select("payments.id",DB::raw("(DATE_FORMAT(payments.paymentdate, '%Y-%m')) as month_year"))->orderBy('payments.paymentdate')->groupBy(DB::raw("DATE_FORMAT(payments.paymentdate, '%Y-%m')"))->where('admissionprocesses.admissionsusersid',$fusers)->whereYear('payments.paymentdate',$monthsdata)->get();
             return view('superadmin.incentivecalculation.filterincentivecalculations',compact('userdata','branchdata','monthsdata','fbys','getusers','fbranchs','fusers','paymr'));
        }
        else if($fbys == 'By Branch')
        {
            $userdata = User::all();
            $branchdata = Branch::all();
            $monthsdata = $request->bstartmonth;
            $fbranchs = $request->branchs;
            $getusers = "";
            $fusers = "";

            $paymr = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select("payments.id",DB::raw("(DATE_FORMAT(payments.paymentdate, '%Y-%m')) as month_year"))->orderBy('payments.paymentdate')->groupBy(DB::raw("DATE_FORMAT(payments.paymentdate, '%Y-%m')"))->where('admissionprocesses.stobranches',$fbranchs)->whereYear('payments.paymentdate',$monthsdata)->get();
             return view('superadmin.incentivecalculation.filterincentivecalculationsforbranchs',compact('userdata','branchdata','monthsdata','fbys','getusers','fbranchs','fusers','paymr'));
        }

      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($fusers,$monthsdata)
    {

            $userdata = User::find($fusers);
            
            return view('superadmin.incentivecalculation.releasepayments',compact('userdata','monthsdata'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($fbranchs,$monthsdata)
    {
         //$userdata = User::find($fusers);
            $psource = PaymentSource::all();
            return view('superadmin.incentivecalculation.releasepaymentsforbranchs',compact('fbranchs','monthsdata','psource'));
    }


    public function releaseincentivesforusers($fusers,$monthsdata)
    {
         //$userdata = User::find($fusers);
            $psource = PaymentSource::all();
            return view('superadmin.incentivecalculation.releasepaymentsforusers',compact('fusers','monthsdata','psource'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($fbranchs,$monthsdata,Request $request)
    {
        $IncentiveReleasePaymentsmodel = new IncentiveReleasePayments();
        $IncentiveReleasePayments = $IncentiveReleasePaymentsmodel->create([
            'incentcollections'=> $request->icollectuobs,
            'mincentivs'=> $request->mincentives,
            'payableincentivespayments'=> $request->payblefess,
            'remainingincentives'=> $request->rinceintives,
            'incpaymentsmodes'=> $request->paymentsmodes,
            'incentivespaymentsdates'=> $request->pdates,
            'ibranchs'=> $request->branhg,
            'mothsof'=> $request->monthsvslue,
        ]);

      

        return redirect('/annual-incentive-calculation')->with('success','Incentives Released successfully!');
    }


     public function storereleaseincentivesforusers($fbranchs,$monthsdata,Request $request)
    {
        $IncentiveReleasePaymentsmodel = new IncentiveReleasePayments();
        $IncentiveReleasePayments = $IncentiveReleasePaymentsmodel->create([
            'incentcollections'=> $request->icollectuobs,
            'mincentivs'=> $request->mincentives,
            'payableincentivespayments'=> $request->payblefess,
            'remainingincentives'=> $request->rinceintives,
            'incpaymentsmodes'=> $request->paymentsmodes,
            'incentivespaymentsdates'=> $request->pdates,
            'iusersids'=> $request->usersdatsd,
            'mothsof'=> $request->monthsvslue,
        ]);

      

        return redirect('/annual-incentive-calculation')->with('success','Incentives Released successfully!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($fbranchs,$monthsdata)
    {   



        $months = explode('-',$monthsdata);
        //dd($months);
            $getreminderdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
         ->join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->where('admissionprocesses.stobranches',$fbranchs)
          ->where('payments.chequetype','PDC Cheque')
          ->whereMonth('payments.paymentdate', '=',$months[1])
          ->whereYear('payments.paymentdate', '=', $months[0])
         ->orderBy('payments.chequedate','DESC')
         ->get();


         $rereminderdata = payment::select('re_admissions.*','payments.*','re_admissions.id as reid','payments.id as pid')
         ->join('re_admissions', 're_admissions.id', '=', 'payments.reinviceid')
          ->where('re_admissions.rstobranches',$fbranchs)
          ->where('payments.chequetype','PDC Cheque')
           ->whereMonth('payments.paymentdate', '=',$months[1])
          ->whereYear('payments.paymentdate', '=', $months[0])
         ->orderBy('payments.chequedate','DESC')
         ->get();


            $pdcdatas = PdcReleasePayments::where('pibranchs',$fbranchs)->whereMonth('pmothsof',$months[1])->whereYear('pmothsof',$months[0])->get();                                       

                                                                

         $psource = PaymentSource::all();


        return view('superadmin.incentivecalculation.pdcreleasepayments',compact('rereminderdata','getreminderdata','monthsdata','fbranchs','psource','pdcdatas'));
    }



     public function releasepdcpaymentsforusers($fusers,$monthsdata)
    {   



        $months = explode('-',$monthsdata);
        //dd($months);
            $getreminderdata = payment::select('admissionprocesses.*','payments.*','admissionprocesses.id as admid','payments.id as pid')
         ->join('admissionprocesses', 'admissionprocesses.id', '=', 'payments.inviceid')
          ->where('admissionprocesses.admissionsusersid',$fusers)
          ->where('payments.chequetype','PDC Cheque')
          ->whereMonth('payments.paymentdate', '=',$months[1])
          ->whereYear('payments.paymentdate', '=', $months[0])
         ->orderBy('payments.chequedate','DESC')
         ->get();


        


            $pdcdatas = PdcReleasePayments::where('piusersids',$fusers)->whereMonth('pmothsof',$months[1])->whereYear('pmothsof',$months[0])->get();                                       

                                                                

         $psource = PaymentSource::all();


        return view('superadmin.incentivecalculation.pdcreleasepaymentsforusers',compact('getreminderdata','monthsdata','fusers','psource','pdcdatas'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $PdcReleasePaymentsmodel = new PdcReleasePayments();
        $PdcReleasePayments = $PdcReleasePaymentsmodel->create([
            'pdcollectionss'=> $request->totalpdccolle,
            'clerchcollections'=> $request->clearchcollections,
            'cheincentives'=> $request->chincentives,
            'pdctotalincentives'=> $request->tincentives,
            'pdcpaidincentives'=> $request->paidincecntives,
            'pdcpayableincentives'=> $request->payblefess,
            'pdcremaininvcentives'=> $request->rinceintives,
            'pdcspmodes'=> $request->paymentsmodes,
            'pdcpaymtnsdates'=> $request->pdates,
            'pibranchs'=> $request->branchs,
            'pmothsof'=> $request->months_data,
        ]);

        return redirect('/annual-incentive-calculation')->with('success','Pdc Amount Released successfully!');
    }


    public function storepdcincentivesdatasusers(Request $request)
    {
        $PdcReleasePaymentsmodel = new PdcReleasePayments();
        $PdcReleasePayments = $PdcReleasePaymentsmodel->create([
            'pdcollectionss'=> $request->totalpdccolle,
            'clerchcollections'=> $request->clearchcollections,
            'cheincentives'=> $request->chincentives,
            'pdctotalincentives'=> $request->tincentives,
            'pdcpaidincentives'=> $request->paidincecntives,
            'pdcpayableincentives'=> $request->payblefess,
            'pdcremaininvcentives'=> $request->rinceintives,
            'pdcspmodes'=> $request->paymentsmodes,
            'pdcpaymtnsdates'=> $request->pdates,
            'piusersids'=> $request->users,
            'pmothsof'=> $request->months_data,
        ]);

        return redirect('/annual-incentive-calculation')->with('success','Pdc Amount Released successfully!');
    }
}
