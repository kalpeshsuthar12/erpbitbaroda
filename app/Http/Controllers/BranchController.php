<?php

namespace App\Http\Controllers;

use App\Branch;
use Illuminate\Http\Request;
use Alert;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Branch $branch)
    {
        $allBranc = $branch::get();

        return view('superadmin.branch.managebranch',compact('allBranc'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.branch.createbranch');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Branch $branch)
    {
        //

          if ($request->hasFile('image')) 
                        {

                            // dd("test");
                            $image = $request->file('image');
                            $imageName = $image->getClientOriginalName();
                            $name = time().'.'.$image->getClientOriginalExtension();
                            $destinationPath = public_path('/branchlogos');
                            $image->move($destinationPath, $imageName);

                             $branchmodel = new Branch();
                            $branchs = $branchmodel->create([
                                'branchname'=> $request->sbranchname,
                                'branchlogo'=> $imageName,
                                'ipaddresses'=> $request->ipaddress,
                                'client_id'=> $request->client_id,
                                'client_code'=> $request->client_code,
                            ]);


                            return redirect('/branch')->with('success','Branch created successfully!');
                        }

                        else
                        {
                               $branchmodel = new Branch();
                            $branchs = $branchmodel->create([
                                'branchname'=> $request->sbranchname,
                                'ipaddresses'=> $request->ipaddress,
                                'client_id'=> $request->client_id,
                                'client_code'=> $request->client_code,
                            ]);


                            return redirect('/branch')->with('success','Branch created successfully!');
                            
                        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Branch $branch)
    {
        $edited = $branch::find($id);

        return view('superadmin.branch.editbranch',compact('edited'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, Branch $branch)
    {
        //

         $updatess = $branch::find($id);
       

        if ($request->hasFile('image')) 
                        {

                            // dd("test");
                            $image = $request->file('image');
                            $imageName = $image->getClientOriginalName();
                            $name = time().'.'.$image->getClientOriginalExtension();
                            $destinationPath = public_path('/branchlogos');
                            $image->move($destinationPath, $imageName);


                             $updatess->branchname = $request->esbranchname;
                             $updatess->branchlogo = $imageName;
                             $updatess->ipaddresses =  $request->ipaddress;
                             $updatess->client_id =  $request->client_id;
                             $updatess->client_code =  $request->client_code;
                             $updatess->save();

                           



                        }

                        else
                        {
                                 $updatess->branchname = $request->esbranchname;
                                 $updatess->ipaddresses =  $request->ipaddress;
                                 $updatess->client_id =  $request->client_id;
                                 $updatess->client_code =  $request->client_code;
                                 $updatess->save();
                        }


       

        return redirect('/branch')->with('success','Branch Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Branch $branch)
    {
        $dele = $branch::find($id);
        $dele->delete();

        return redirect('/branch')->with('success','Branch Deleted successfully!');
    }
}
