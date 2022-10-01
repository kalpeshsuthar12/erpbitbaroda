<?php

namespace App\Http\Controllers;

use App\times;
use Illuminate\Http\Request;

class TimesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tis = times::all();

        return view('superadmin.batchtimi.manage',compact('tis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.batchtimi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $timesmodel = new times();
        $times = $timesmodel->create([
            'timesname'=> $request->tmsname,
        ]);

      

        return redirect('/times')->with('success','Time created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\times  $times
     * @return \Illuminate\Http\Response
     */
    public function show(times $times)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\times  $times
     * @return \Illuminate\Http\Response
     */
    public function edit($id,times $times)
    {
        $eidday = times::find($id);

        return view('superadmin.batchtimi.edit',compact('eidday'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\times  $times
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, times $times)
    {
         $upd = times::find($id);
        $upd->timesname = $request->tmsname;
        $upd->save();

        return redirect('/times')->with('success','Time Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\times  $times
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,times $times)
    {
        $del = times::find($id);
        $del->delete();

        return redirect('/times')->with('success','Time Deleted successfully!');
    }
}
