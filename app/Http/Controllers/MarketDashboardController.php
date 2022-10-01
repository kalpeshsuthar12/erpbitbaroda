<?php

namespace App\Http\Controllers;
use App\assigntarget;
use App\TargetAlloted;
use App\leads;
use App\leadsfollowups;
use App\followup;
use App\Branch;
use App\invoices;
use App\payment;
use App\admissionprocess;
use App\User;
use Auth;
use Illuminate\Http\Request;

class MarketDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($fromDates)
    {
        $userId = Auth::user()->id;
         $newDats = explode("-", $fromDates);
        $leadsdatas = leads::where('user_id',$userId)->whereYear('leaddate', $newDats[0])->whereMonth('leaddate', $newDats[1])->count();

        return response()->json($leadsdatas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($fromDates)
    {
        $userId = Auth::user()->id;
         $newDats = explode("-", $fromDates);
        $conversionstatus = admissionprocess::where('admissionsusersid',$userId)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->count();
        return response()->json($conversionstatus);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($fromDates)
    {
        $userId = Auth::user()->id;
         $newDats = explode("-", $fromDates);
        $invodata = admissionprocess::where('admissionsusersid',$userId)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->sum('invtotal');
        return response()->json($invodata);

         
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($fromDates)
    {
        $dates = date('Y-m-d');
        $username = Auth::user()->name;
         $newDats = explode("-", $fromDates);
        //$currentMonth = date('m');
      //  dd($enddates);
            $tshid = assigntarget::where('tassignuser',$username)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
          //  dd($tshid);
            /*$targetdata = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();*/

            $tdata  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
               if ($tdata) 
               {

                        $targetdata = $tdata->totaltargets;
               }

               else
               {
                    $targetdata = 0;
               }

             return response()->json($targetdata);
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($fromDates)
    {
        $userId = Auth::user()->id;
         $newDats = explode("-", $fromDates);

        $pid = admissionprocess::where('admissionsusersid',$userId)->pluck('id');

        $paumentdats = payment::where('inviceid',$pid)->whereYear('paymentdate', $newDats[0])->whereMonth('paymentdate', $newDats[1])->sum('paymentreceived');
        return response()->json($paumentdats);
        
    }   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($fromDates)
    {
        $userId = Auth::user()->id;
        $newDats = explode("-", $fromDates);
        $username = Auth::user()->name;
        $tshid = assigntarget::where('tassignuser',$username)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
          //  dd($tshid);
            /*$targetdata = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();*/

            $tdata  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
               if ($tdata) 
               {

                        $targetdata = $tdata->totaltargets;
               }

               else
               {
                    $targetdata = 0;
               }
        $pid = admissionprocess::where('admissionsusersid',$userId)->pluck('id');
        
        $paumentdats = payment::where('inviceid',$pid)->whereBetween('paymentdate',[$fromDates, $toDates])->sum('paymentreceived');
        
        $incent = $targetdata - $paumentdats;

        return response()->json($incent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($fromDates)
    {
        $userId = Auth::user()->id;
        $newDats = explode("-", $fromDates);
        $username = Auth::user()->name;
        $tshid = assigntarget::where('tassignuser',$username)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
          //  dd($tshid);
            /*$targetdata = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();*/

            $tdata  = TargetAlloted::where('targetuserid',$tshid)->where('statsus',0)->orderBy('id','DESC')->first();
               if ($tdata) 
               {

                        $targetdata = $tdata->totaltargets;
               }

               else
               {
                    $targetdata = 0;
               }
        $pid = admissionprocess::where('admissionsusersid',$userId)->pluck('id');
        
        $paumentdats = payment::where('inviceid',$pid)->whereBetween('paymentdate',[$fromDates, $toDates])->sum('paymentreceived');
        
        $incent = $targetdata - $paumentdats;
       // dd($incent);

         if($incent != 0)
             {
               
               $print = "<h5 class='mb-1 mt-1 text-red blink-hard'>You Have Not Achieved Target</h5>";
                return response()->json($print);
                    
             }

             else
             {
                

                 $print = "<h5 class='mb-1 mt-1 text-green blink-hard'>You Achieved Target</h5><p class='text-muted mb-0'>";
                 return response()->json($print);
             }


    }
}
