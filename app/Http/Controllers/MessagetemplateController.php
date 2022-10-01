<?php

namespace App\Http\Controllers;

use App\messagetemplate;
use Illuminate\Http\Request;

class MessagetemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(messagetemplate $messagetemplate)
    {
        $messa = messagetemplate::get();

        return view('superadmin.messagetemplates.manage',compact('messa'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.messagetemplates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,messagetemplate $messagetemplate)
    {
        $messagetemplatemodel = new messagetemplate();
        $messagetemplate = $messagetemplatemodel->create([
            'messagename'=> $request->messagename,
            'messagedetails'=> $request->messatemplates,
        ]);
        return redirect('/message-template')->with('success','Message Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\messagetemplate  $messagetemplate
     * @return \Illuminate\Http\Response
     */
    public function show(messagetemplate $messagetemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\messagetemplate  $messagetemplate
     * @return \Illuminate\Http\Response
     */
    public function edit($id,messagetemplate $messagetemplate)
    {
        $messa = messagetemplate::find($id);
        return view('superadmin.messagetemplates.edit',compact('messa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\messagetemplate  $messagetemplate
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, messagetemplate $messagetemplate)
    {
        //u

        $updatess = messagetemplate::find($id);
        $updatess->messagename = $request->messagename;
        $updatess->messagedetails = $request->messatemplates;
        $updatess->save(); 

        return redirect('/message-template')->with('success','Message Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\messagetemplate  $messagetemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,messagetemplate $messagetemplate)
    {
        $deleds = messagetemplate::find($id);   
        $deleds->delete();  

        return redirect('/message-template')->with('success','Message Deleted Successfully!!');   
    }
}
