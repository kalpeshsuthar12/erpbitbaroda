<?php

namespace App\Http\Controllers;

use App\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Source $source)
    {
        $sources = $source::get();
        return view('superadmin.sources.manage',compact('sources'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.sources.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Source $source)
    {
        //

         $sourcemodel = new source();
        $sources = $sourcemodel->create([
            'sourcename'=> $request->sname,
        ]);

      

        return redirect('/sources')->with('success','Sources created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function show(Source $source)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Source $source)
    {
        $ed = $source::find($id);
        return view('superadmin.sources.edit',compact('ed'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, Source $source)
    {
        //

        $updated = $source::find($id);
        $updated->sourcename = $request->esname;
        $updated->save();

        return redirect('/sources')->with('success','Source Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Source $source)
    {
        //
        $dele = $source::find($id);
        $dele->delete();

        return redirect('/sources')->with('success','Source Deleted Successfully!');
    }
}
