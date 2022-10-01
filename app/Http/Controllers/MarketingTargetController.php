<?php

namespace App\Http\Controllers;
use App\assigntarget;
use App\TargetAlloted;
use App\payment;
use App\admissionprocess; 
use Illuminate\Http\Request;
use Auth;

class MarketingTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(assigntarget $assigntarget)
    {

         $userId = Auth::user()->name;

         $targetsdata = assigntarget::where('tassignuser',$userId)->get();
         return view('marketing.target.manage',compact('targetsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $currentMonth = date('m');

        $userId = Auth::user()->id;

        $userName = Auth::user()->name;
        
        $admissionId = admissionprocess::select('payments.paymentreceived')->join("payments","admissionprocesses.id","=","payments.inviceid")->where('admissionprocesses.admissionsusersid',$userId)->whereMonth('payments.paymentdate', $currentMonth)->sum('payments.paymentreceived');

        $Tars = assigntarget::select('assigntargets.*','target_alloteds.*')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tassignuser',$userName)->whereMonth('assigntargets.enddates',$currentMonth)->where('target_alloteds.statsus',1)->get();

       
    
        return view('marketing.target.incentcalcu',compact('admissionId','Tars'));


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
         $userName = Auth::user()->name;


        $targetmonths = $request->nameMonth;

        $newDats = explode("-", $targetmonths);
      //  dd($newDats);


        $admissionId = admissionprocess::select('payments.paymentreceived')->join("payments","admissionprocesses.id","=","payments.inviceid")->where('admissionprocesses.admissionsusersid',$userId)->whereYear('payments.paymentdate', $newDats[0])->whereMonth('payments.paymentdate', $newDats[1])->sum('payments.paymentreceived');


        $Tars = assigntarget::select('assigntargets.*','target_alloteds.*')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tassignuser',$userName)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->get();
        
        $TotalsTargest = assigntarget::select('target_alloteds.totaltargets')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tassignuser',$userName)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->sum('target_alloteds.totaltargets');

       //dd($TotalsTargest);
        return view('marketing.target.filterbycalculations',compact('admissionId','Tars','TotalsTargest'));
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
