<?php

namespace App\Http\Controllers;
use App\HrDocumentSave;
use App\User;
use Illuminate\Http\Request;

class AdminHrDocumentSaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hrall = HrDocumentSave::all();

        return view('admin.hrdocumentssaves.manage',compact('hrall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $uall = User::where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('userstatus',1)->get();

            return view('admin.hrdocumentssaves.create',compact('uall'));
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

        return redirect('/admin-employee-documents-saves-online')->with('success','Employee Document Save Successfully!!');
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
