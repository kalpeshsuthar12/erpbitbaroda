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

class CentreManagerBranchDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($BrStartDate)
    {
        $UserBranch = Auth::user()->branchs;
        $newDats = explode("-", $BrStartDate);
         $leadsdatas = leads::where('branch',$UserBranch)->whereYear('leaddate', $newDats[0])->whereMonth('leaddate', $newDats[1])->count();

        return response()->json($leadsdatas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($BrStartDate)
    {
        $UserBranch = Auth::user()->branchs;
         $newDats = explode("-", $BrStartDate);
        $conversionstatus = admissionprocess::where('stobranches',$UserBranch)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->count();
        return response()->json($conversionstatus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($BrStartDate)
    {
        $UserBranch = Auth::user()->branchs;
         $newDats = explode("-", $BrStartDate);
         $invodata = admissionprocess::where('stobranches',$UserBranch)->whereYear('sadate', $newDats[0])->whereMonth('sadate', $newDats[1])->sum('invtotal');
        return response()->json($invodata);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($BrStartDate)
    {
             $UserBranch = Auth::user()->branchs;
             $newDats = explode("-", $BrStartDate);

       
            $tshid = assigntarget::where('tbranch',$UserBranch)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
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
    public function edit($BrStartDate)
    {
         $UserBranch = Auth::user()->branchs;
          $newDats = explode("-", $BrStartDate);

        //$pid = admissionprocess::where('stobranches',$UserBranch)->pluck('id');
        //dd($pid);

      

            $paumentdats = payment::Where('branchs',$UserBranch)->whereYear('paymentdate', $newDats[0])->whereMonth('paymentdate', $newDats[1])->sum('paymentreceived');
            //dd($paumentdats);
                return response()->json($paumentdats);
        
       // / dd($paumentdats);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($BrStartDate)
    {
         $UserBranch = Auth::user()->branchs;
         $newDats = explode("-", $BrStartDate);
       $tshid = assigntarget::where('tbranch',$UserBranch)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
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
       $paumentdats = payment::Where('branchs',$UserBranch)->whereYear('paymentdate', $newDats[0])->whereMonth('paymentdate', $newDats[1])->sum('paymentreceived');
         $incent = $targetdata - $paumentdats;
       //  dd($incent);
      
        return response()->json($incent);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($BrStartDate)
    {
         $UserBranch = Auth::user()->branchs;
          $newDats = explode("-", $BrStartDate);
       $tshid = assigntarget::where('tbranch',$UserBranch)->whereYear('enddates', $newDats[0])->whereMonth('enddates', $newDats[1])->pluck('id');
            
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
       $paumentdats = payment::Where('branchs',$UserBranch)->whereYear('paymentdate', $newDats[0])->whereMonth('paymentdate', $newDats[1])->sum('paymentreceived');
        
         $incent = $targetdata - $paumentdats;
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
