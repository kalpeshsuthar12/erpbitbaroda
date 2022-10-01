<?php

namespace App\Http\Controllers;
use App\UserPunching;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PunchingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('superadmin.punchin.create');
    }

    public function userattendances()
    {
         $userall = UserPunching::join('users','users.id','=','user_punchings.pusersid')->select('users.*','user_punchings.*')->get();
        return view('superadmin.punchin.manage',compact('userall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userIds = Auth::user()->id;
        $todays = date('Y-m-d');

                    $a = $request->ptimes;
                    $punchins = Carbon::parse($a)->format('H:i');
                  //  dd($punchins);

        
        $UserPunchingmodel = new UserPunching();
        $UserPunching = $UserPunchingmodel->create([
            'pusersid'=> $userIds,
            'title'=> 'Presents',
            'puncdates'=> $todays,
            'punch_in'=> $punchins,
        ]);

      

        return redirect()->back()->with('success','Punch-In successfully Done');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
            $pun = UserPunching::find($id);

        return view('superadmin.punchin.punchout',compact('pun'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {

        $a = $request->ptimes;
        $punchouts = Carbon::parse($a)->format('H:i');
         $pun = UserPunching::find($id);
         $pun->punch_out = $punchouts;
         $pun->save();

         return redirect()->back()->with('success','Punch-Out successfully Done');
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
