<?php

namespace App\Http\Controllers;
use App\assigntarget;
use App\TargetAlloted;
use App\payment;
use App\admissionprocess; 
use Illuminate\Http\Request;
use Auth;
use DB;

class CenterManagerTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->name;
        $userbranchs = Auth::user()->branchs;

         $targetsdata = assigntarget::where('tbranch',$userbranchs)->get();
         return view('centremanager.target.manage',compact('targetsdata'));
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

        $userBranch = Auth::user()->branchs;

        $userName = Auth::user()->name;
        
        $admissionId = admissionprocess::select('payments.paymentreceived')->join("payments","admissionprocesses.id","=","payments.inviceid")->where('admissionprocesses.stobranches',$userBranch)->whereMonth('payments.paymentdate', $currentMonth)->sum('payments.paymentreceived');

        $Tars = assigntarget::select('assigntargets.*','target_alloteds.*')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tbranch',$userBranch)->where('target_alloteds.statsus',1)->whereMonth('assigntargets.enddates',$currentMonth)->get();

         return view('centremanager.target.incentcalcu',compact('admissionId','Tars'));

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $userBranch = Auth::user()->branchs;

        $targetmonths = $request->nameMonth;

        $newDats = explode("-", $targetmonths);
      //  dd($newDats);


        $admissionId = admissionprocess::select('payments.paymentreceived')->join("payments","admissionprocesses.id","=","payments.inviceid")->where('admissionprocesses.stobranches',$userBranch)->whereYear('payments.paymentdate', $newDats[0])->whereMonth('payments.paymentdate', $newDats[1])->sum('payments.paymentreceived');


        $Tars = assigntarget::select('assigntargets.*','target_alloteds.*')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tbranch',$userBranch)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->get();
        
        $TotalsTargest = assigntarget::select('target_alloteds.totaltargets')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tbranch',$userBranch)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->sum('target_alloteds.totaltargets');

       //dd($TotalsTargest);
        return view('centremanager.target.filterbycalculations',compact('admissionId','Tars','TotalsTargest'));

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
