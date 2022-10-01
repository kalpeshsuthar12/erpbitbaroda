<?php

namespace App\Http\Controllers;

use App\coursesubcategory;
use App\coursecategory;
use App\brocheure;
use DB;
use Illuminate\Http\Request;

class BrocheureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brocheuredata =DB::table('brocheures')
                ->join('coursecategories', 'coursecategories.id', '=', 'brocheures.categ_id')
                ->join('coursesubcategories', 'coursesubcategories.id', '=', 'brocheures.subcateg_id')
                ->select('brocheures.id','coursesubcategories.subcategory_name', 'coursecategories.coursecategoryname','brocheures.created_at','brocheures.mcoursename','brocheures.coursesurls','brocheures.brocheuresfiles')
                ->get();

                return view('superadmin.markertingmaterials.managebrocheure',compact('brocheuredata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(coursecategory $coursecategory)
    {
        $coursecat = coursecategory::get();
        return view('superadmin.markertingmaterials.createbrocheure',compact('coursecat'));      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,brocheure $brocheure)
    {
        //dd($request->all());
         if ($request->hasFile('image')) 
         {
                // dd("test");
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/marketingbrocheure');
                $image->move($destinationPath, $imageName);



                        $brocheuremodel = new brocheure();
                        $brocheure = $brocheuremodel->create([
                            'categ_id'=> $request->ccategory,
                            'subcateg_id'=> $request->csubcategory,
                            'mcoursename'=> $request->mcoursename,
                            'brocheuresfiles'=> $imageName,
                            'coursesurls'=> $request->websites,
                           
                        ]);

                return redirect('/brocheures')->with('success','Brocheures Added successfully!');
        }
       
        //return redirect('/course')->with('success','Course created successfully!');
         
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\brocheure  $brocheure
     * @return \Illuminate\Http\Response
     */
    public function show(brocheure $brocheure)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\brocheure  $brocheure
     * @return \Illuminate\Http\Response
     */
    public function edit($id,brocheure $brocheure,coursesubcategory $coursesubcategory,coursecategory $coursecategory)
    {
        $cat = coursecategory::all();
        $subcat = coursesubcategory::all();
        $broche = brocheure::find($id);

        return view('superadmin.markertingmaterials.editbrocheure',compact('cat','subcat','broche'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\brocheure  $brocheure
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, brocheure $brocheure)
    {
        $updat = brocheure::find($id);
        

         if ($request->hasFile('image')) 
         {
                // dd("test");
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/marketingbrocheure');
                $image->move($destinationPath, $imageName);



                        $updat->categ_id = $request->ccategory;
                        $updat->subcateg_id = $request->csubcategory;
                        $updat->mcoursename = $request->coursename;
                        $updat->coursesurls = $request->websites;
                        $updat->brocheuresfiles = $imageName;
                        $updat->save();

                 return redirect('/brocheures')->with('success','Brocheures Updated successfully!');
        }
        else
        {
                        $updat->categ_id = $request->ccategory;
                        $updat->subcateg_id = $request->csubcategory;
                        $updat->mcoursename = $request->coursename;
                        $updat->coursesurls = $request->websites;
                        $updat->save();
          
            
     
        return redirect('/brocheures')->with('success','Brocheures Updated successfully!');
         
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\brocheure  $brocheure
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,brocheure $brocheure)
    {
        $deled = brocheure::find($id);
        $deled->delete();

        return redirect('/brocheures')->with('success','Brocheure Deleted successfully!');
    }
}
