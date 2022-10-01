<?php

namespace App\Http\Controllers;

use App\AdvanceTaken;
use App\PaymentSource;
use App\User;
use Illuminate\Http\Request;

class AdvanceTakenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $asdavncesdata = User::join('advance_takens','advance_takens.ausersid','=','users.id')->select('users.name','advance_takens.*')->orderBy('id','DESC')->get();

        return view('superadmin.advancetaken.manage',compact('asdavncesdata'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pall = PaymentSource::all();
        $uall = User::all();

        return view('superadmin.advancetaken.create',compact('pall','uall'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $AdvanceTakenmodel = new AdvanceTaken();
        $AdvanceTaken = $AdvanceTakenmodel->create([
            'ausersid'=> $request->usausers,
            'atkndate'=> $request->adates,
            'atkamounts'=> $request->adamounts,
            'atkmode'=> $request->apaymodes,
        ]); 


        return redirect('/advance-taken')->with('success','Advance Successfully Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AdvanceTaken  $advanceTaken
     * @return \Illuminate\Http\Response
     */
    public function show(AdvanceTaken $advanceTaken)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AdvanceTaken  $advanceTaken
     * @return \Illuminate\Http\Response
     */
    public function edit($id,AdvanceTaken $advanceTaken)
    {
        $advan = AdvanceTaken::find($id);

         $pall = PaymentSource::all();
        $uall = User::all();

        return view('superadmin.advancetaken.edit',compact('pall','uall','advan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AdvanceTaken  $advanceTaken
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, AdvanceTaken $advanceTaken)
    {    

            $updates = AdvanceTaken::find($id);
            $updates->ausersid = $request->usausers;
            $updates->atkndate  = $request->adates;
            $updates->atkamounts  = $request->adamounts;
            $updates->atkmode  = $request->apaymodes;
            $updates->save();


            return redirect('/advance-taken')->with('success','Advance Successfully Updated!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AdvanceTaken  $advanceTaken
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,AdvanceTaken $advanceTaken)
    {
         $deletes = AdvanceTaken::find($id);
         $deletes->delete();

         return redirect('/advance-taken')->with('success','Advance Successfully Deleted!!');

    }
}
