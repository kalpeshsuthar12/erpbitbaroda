<?php

namespace App\Http\Controllers;

use App\followup;
use Illuminate\Http\Request;

class FollowupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(followup $followup)
    {
        $fol = followup::get();
        return view('superadmin.followups.manage',compact('fol'));
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('superadmin.followups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,followup $followup)
    {
         $followupmodel = new followup();
        $followup = $followupmodel->create([
            'followupname'=> $request->fname,
        ]);

        return redirect('/followups')->with('success','Followups created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\followup  $followup
     * @return \Illuminate\Http\Response
     */
    public function show(followup $followup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\followup  $followup
     * @return \Illuminate\Http\Response
     */
    public function edit($id,followup $followup)
    {
        //

        $edis = followup::find($id);

        return view('superadmin.followups.edit',compact('edis'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\followup  $followup
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, followup $followup)
    {
        $updated = followup::find($id);
        $updated->followupname = $request->fname;
        $updated->save();

        return redirect('/followups')->with('success','Followups Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\followup  $followup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,followup $followup)
    {
         $deletes = followup::find($id);
         $deletes->delete();
         return redirect('/followups')->with('success','Followups Deleted Successfully');
    }
}
