<?php

namespace App\Http\Controllers;

use App\days;
use Illuminate\Http\Request;

class DaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $da = days::all();

        return view('superadmin.days.manage',compact('da'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.days.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $daysmodel = new days();
        $days = $daysmodel->create([
            'daysname'=> $request->daysname,
        ]);

      

        return redirect('/days')->with('success','Days created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\days  $days
     * @return \Illuminate\Http\Response
     */
    public function show(days $days)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\days  $days
     * @return \Illuminate\Http\Response
     */
    public function edit($id,days $days)
    {
        $eidday = days::find($id);

        return view('superadmin.days.edit',compact('eidday'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\days  $days
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, days $days)
    {
        $upd = days::find($id);
        $upd->daysname = $request->daysname;
        $upd->save();

         return redirect('/days')->with('success','Days Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\days  $days
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,days $days)
    {
        $deles = days::find($id);
        $deles->delete();
         return redirect('/days')->with('success','Days Deleted successfully!');
    }
}
