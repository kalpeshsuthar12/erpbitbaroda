<?php

namespace App\Http\Controllers;
use App\reference;
use App\User;
use App\course;
use App\logsData;
use Auth;
use Illuminate\Http\Request;

class CentreCoordinatorReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $user = Auth::user()->id;
         $username = Auth::user();
        $refersdata = reference::where('userid','=',$user)->get();
        //dd()

        return view('centrecoordinator.references.manage',compact('refersdata'));
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
         return view('centrecoordinator.references.create',compact('usersdata','coursedata'));
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
        $username = Auth::user();
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

         

        if($incentiveby == "Cash")
        {
            $newamount = $request->amounts;
        }
        elseif($incentiveby == "%")
         {
             $newamount = $request->percnet;
         }

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
            'userid'=> $user,
        ]);

        /*$assigntarget['user_id'] = $user->id;*/

        if ($reference) {
            $logsDatamodel = new logsData();
        $logsData = $logsDatamodel->create([
            'logsdescription'=> 'References Created By '.$username->name,
            
        ]);

          
            return redirect('/centre-coordinator-reference')->with('success','References Created Successfully');
        } 
        else {
           
             return redirect('/centre-coordinator-reference')->with('success','Oops something went wrong, References not saved');
        }
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

         return view('centrecoordinator.references.edit',compact('refe','usersdata','coursedata'));
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
