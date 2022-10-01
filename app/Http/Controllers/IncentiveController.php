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
use Auth;

class IncentiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $userdata = User::all();
        $branchdata = Branch::all();
        $paymr = payment::select("id",DB::raw("(DATE_FORMAT(paymentdate, '%Y-%m')) as month_year"))->orderBy('paymentdate')->groupBy(DB::raw("DATE_FORMAT(paymentdate, '%Y-%m')"))->get();


        return view('superadmin.incentives.manage',compact('userdata','branchdata','paymr'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fusers = Auth::user()->id;

          $paymr = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select("payments.id",DB::raw("(DATE_FORMAT(payments.paymentdate, '%Y-%m')) as month_year"))->orderBy('payments.paymentdate')->groupBy(DB::raw("DATE_FORMAT(payments.paymentdate, '%Y-%m')"))->where('admissionprocesses.admissionsusersid',$fusers)->get();
            
             return view('marketing.incentives.manage',compact('fusers','paymr'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fbranchs = Auth::user()->branchs;

        $paymr = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select("payments.id",DB::raw("(DATE_FORMAT(payments.paymentdate, '%Y-%m')) as month_year"))->orderBy('payments.paymentdate')->groupBy(DB::raw("DATE_FORMAT(payments.paymentdate, '%Y-%m')"))->where('admissionprocesses.stobranches',$fbranchs)->get();

          return view('centremanager.incentives.manage',compact('paymr'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($fusers,$monthsdata)
    {
        $months = explode('-',$monthsdata);

        $incents = IncentiveReleasePayments::where('iusersids',$fusers)->whereMonth('mothsof',$months[1])->whereYear('mothsof',$months[0])->get();
        return view('superadmin.incentivecalculation.viewpureincentiveannual',compact('incents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($fbranchs,$monthsdata)
    {
        
        $months = explode('-',$monthsdata);

        $incents = IncentiveReleasePayments::where('ibranchs',$fbranchs)->whereMonth('mothsof',$months[1])->whereYear('mothsof',$months[0])->get();
        return view('superadmin.incentivecalculation.viewpureincentiveannualforbranch',compact('incents'));
    
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
