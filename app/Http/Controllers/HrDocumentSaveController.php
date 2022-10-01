<?php

namespace App\Http\Controllers;

use App\HrDocumentSave;
use App\User;
use Illuminate\Http\Request;

class HrDocumentSaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hrall = HrDocumentSave::all();

        return view('superadmin.hrdocumentssaves.manage',compact('hrall'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $uall = User::where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('userstatus',1)->get();

            return view('superadmin.hrdocumentssaves.create',compact('uall'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $usersnames = User::find($request->usersdata);
        $HrDocumentSavemodel = new HrDocumentSave();
                        $HrDocumentSave = $HrDocumentSavemodel->create([
                            'hdsusersids'=> $request->usersdata,
                            'hdsusername'=> $usersnames->name,
                            'hduselinks'=> $request->dlinks,
                            
                            ]);

        return redirect('/employee-documents-saves-online')->with('success','Employee Document Save Successfully!!');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HrDocumentSave  $hrDocumentSave
     * @return \Illuminate\Http\Response
     */
    public function show(HrDocumentSave $hrDocumentSave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrDocumentSave  $hrDocumentSave
     * @return \Illuminate\Http\Response
     */
    public function edit($id,HrDocumentSave $hrDocumentSave)
    {
        $edi = HrDocumentSave::find($id);
        $uall = User::where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('userstatus',1)->get();

        return view('superadmin.hrdocumentssaves.edit',compact('edi','uall'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrDocumentSave  $hrDocumentSave
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, HrDocumentSave $hrDocumentSave)
    {
        

        $usersnames = User::find($request->usersdata);
        $upoda = HrDocumentSave::find($id);

        $upoda->hdsusersids = $request->usersdata;
        $upoda->hdsusername = $usersnames->name;
        $upoda->hduselinks =  $request->dlinks;

        return redirect('/employee-documents-saves-online')->with('success','Employee Document Updated Successfully!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrDocumentSave  $hrDocumentSave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,HrDocumentSave $hrDocumentSave)
    {
        $deles = HrDocumentSave::find($id);
        $deles->delete();

        return redirect('/employee-documents-saves-online')->with('success','Employee Document Deleted Successfully!!');
    }
}
