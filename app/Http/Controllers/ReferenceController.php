<?php

namespace App\Http\Controllers;
use App\PaymentSource;
use App\reference;
use App\admissionprocess;
use App\User;
use App\course;
use App\logsData;
use Illuminate\Http\Request;
use Auth;

class ReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $refersdata = reference::select('admissionprocesses.id as aids','admissionprocesses.sphone','references.*')->leftjoin('admissionprocesses','admissionprocesses.sphone','=','references.rphone')->get(); 
      // $refersdata = reference::all();

        return view('superadmin.references.manage',compact('refersdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $usersdata = User::all();
         $coursedata = course::get();
         return view('superadmin.references.create',compact('usersdata','coursedata'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $user = Auth::user();
         // dd($request->all());
          $rfrom = $request->rfrom;
          $rname = $request->rname;
          $rmobile = $request->rmobile;
          $assignto = $request->assignto;
          $course = $request->course;
          $fees = $request->fees;
          $incentiveby = $request->incentiveby;
          $ima = $request->ima;
          $pdate = $request->pdate;
          $pmode = $request->pmode;
          $status = $request->status;
          $descsr = $request->descript;

         

       
             $newamount = $request->percnet;
         

           $referencemodel = new reference();
        $reference = $referencemodel->create([
            'referencefrom'=> $request->rfrom,
            'referencename'=> $request->rname,
            'rphone'=> $request->rmobile,
            'assignto'=> $request->assignto,
            'courses'=> $request->course,
            'fees'=> $fees,
            'incentiveby'=> $request->incentiveby,
            'incentive'=> $newamount,
            'iamounts'=> $request->ima,
            'paynmentdate'=> $pdate,
            'paymentmode'=> $pmode,
            'status'=> $status,
            'descriptiions'=> $descsr,
        ]);

        /*$assigntarget['user_id'] = $user->id;*/

        if ($reference) {
            $logsDatamodel = new logsData();
        $logsData = $logsDatamodel->create([
            'logsdescription'=> 'References Created By '.$user->name,
            
        ]);

          
            return redirect('/references')->with('success','References Created Successfully');
        } 
        else {
           
             return redirect('/references')->with('success','Oops something went wrong, References not saved');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function show($id,reference $reference)
    {
         $invamounts = admissionprocess::find($id);
        $refenevalues = reference::where('rphone',$invamounts->sphone)->first();
     //   dd($refenevalues);
        $psource = PaymentSource::all();

        return view('superadmin.references.clearreferencesincentives',compact('invamounts','refenevalues','psource'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function edit($id,reference $reference)
    {
        $refe = reference::find($id);
        $usersdata = User::all();
         $coursedata = course::get();

         return view('superadmin.references.edit',compact('refe','usersdata','coursedata'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, reference $reference)
    {
       // dd($request->all());
         $incentiveby = $request->incentiveby;
           $newamount = $request->percnet;
            


        $updatereferences = reference::find($id);
        $updatereferences->referencefrom = $request->rfrom;
        $updatereferences->referencename =  $request->rname;
        $updatereferences->assignto =  $request->assignto;
        $updatereferences->rphone =  $request->rmobile;
        $updatereferences->incentiveby =  $incentiveby;
        $updatereferences->incentive =  $newamount;
        $updatereferences->descriptiions =  $request->descript;
        $updatereferences->save();

        return redirect('/references')->with('success','References Updated Successfully');
    }

    public function delete($id)
    {
         $updatse = reference::find($id);
        $updatse->iamounts = $request->incentives;
        $updatse->paymentmode = $request->pmodes;
        $updatse->paynmentdate = $request->pdates;
        $updatse->status = "Paid";
        $updatse->save();

        return redirect('/references')->with('success','References Incentive Clear Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\reference  $reference
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,reference $reference)
    {
        $user = Auth::user();
        $dels = reference::find($id);
        $dels->delete();
       

        if ($dels) {
            $logsDatamodel = new logsData();
        $logsData = $logsDatamodel->create([
            'logsdescription'=> 'Reference Deleted By '.$user->name,
            
        ]);

          
            return redirect('/references')->with('success','References Deleted Successfully');
        } 
        else {
           
             return redirect('/delete-references/'.$updatedsdata->id)->with('success','Oops something went wrong, References not saved');
        }
    }

    public function ajax($leadsphones)
    {
        $referencese = reference::where('rphone','=',$leadsphones)->first();

        return response()->json($referencese);
    }
}
