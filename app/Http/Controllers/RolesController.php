<?php

namespace App\Http\Controllers;

use App\Roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Roles $roles)
    {
        
        $rolesdata = Roles::get();
        return view('superadmin.roles.manage',compact('rolesdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Roles $roles)
    {
        $rolesmodel = new Roles();
        $roles = $rolesmodel->create([
            'rolesname'=> $request->rolename,
        ]);

      

        return redirect('/roles')->with('success','Roles created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function show(Roles $roles)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Roles $roles)
    {
        $edites = Roles::find($id);

        return view('superadmin.roles.edit',compact('edites'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, Roles $roles)
    {
        $upda = Roles::find($id);
        $upda->rolesname = $request->rolename;
        $upda->save();

         return redirect('/roles')->with('success','Roles Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Roles  $roles
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Roles $roles)
    {
        $deles = Roles::find($id);
        $deles->delete();
        return redirect('/roles')->with('success','Roles Deleted successfully!');
    }
}
