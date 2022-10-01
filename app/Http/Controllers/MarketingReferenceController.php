<?php

namespace App\Http\Controllers;

use App\reference;
use App\User;
use App\course;
use App\logsData;
use Illuminate\Http\Request;
use Auth;


class MarketingReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $user = Auth::user()->id;
         $username = Auth::user()->name;
       
        $refersdata = reference::select('admissionprocesses.id as aids','admissionprocesses.sphone','references.*')->leftjoin('admissionprocesses','admissionprocesses.sphone','=','references.rphone')->where('assignto','=',$username)->get();
        //dd()

        return view('marketing.references.manage',compact('refersdata'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $usersdata = Auth::user()->name;
         $coursedata = course::get();
         return view('marketing.references.create',compact('usersdata','coursedata'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user()->id;
        $userBranch = Auth::user()->branchs;
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
            'rwhatsapp'=> $request->rwhatsappno,
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
            'abranchs'=> $userBranch,
        ]);

        /*$assigntarget['user_id'] = $user->id;*/

       
             return redirect('/marketing-references')->with('success','Oops something went wrong, References not saved');
        
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
        $refe = reference::find($id);
        $usersdata = User::all();
         $coursedata = course::get();

         return view('marketing.references.edit',compact('refe','usersdata','coursedata'));
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
        $userBranch = Auth::user()->branchs;
         $incentiveby = $request->incentiveby;
           $newamount = $request->percnet;
 
        $updatereferences = reference::find($id);
        $updatereferences->referencefrom = $request->rfrom;
        $updatereferences->referencename =  $request->rname;
        $updatereferences->assignto =  $request->assignto;
        $updatereferences->rphone =  $request->rmobile;
        $updatereferences->rwhatsapp =  $request->rwhatsappno;
        $updatereferences->incentiveby =  $incentiveby;
        $updatereferences->incentive =  $newamount;
        $updatereferences->descriptiions =  $request->descript;
        $updatereferences->abranchs =  $userBranch;
        $updatereferences->save();

        return redirect('/marketing-references')->with('success','References Updated Successfully');
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

    public function ajax($leadsphones)
    {
        $referencese = reference::where('rphone','=',$leadsphones)->first();

        return response()->json($referencese);
    }
}
