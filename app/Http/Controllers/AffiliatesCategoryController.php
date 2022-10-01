<?php

namespace App\Http\Controllers;

use App\AffiliatesCategory;
use Illuminate\Http\Request;

class AffiliatesCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $afnames = AffiliatesCategory::all();
        return view('superadmin.affiliatescategory.manage',compact('afnames'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.affiliatescategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         if ($request->hasFile('image')) 
                        
            {

                            // dd("test");
                            $image = $request->file('image');
                            $imageName = $image->getClientOriginalName();
                            $name = time().'.'.$image->getClientOriginalExtension();
                            $destinationPath = public_path('/aggrementsdocuments');
                            $image->move($destinationPath, $imageName);



                 $AffiliatesCategorymodel = new AffiliatesCategory();
                $AffiliatesCategory = $AffiliatesCategorymodel->create([
                    'acategoriesname'=> $request->acname,
                ]);


                return redirect('/affiliates-category')->with('success','Affiliates Category created successfully!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AffiliatesCategory  $affiliatesCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AffiliatesCategory $affiliatesCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AffiliatesCategory  $affiliatesCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,AffiliatesCategory $affiliatesCategory)
    {
        $editid = AffiliatesCategory::find($id);

        return view('superadmin.affiliatescategory.edit',compact('editid'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AffiliatesCategory  $affiliatesCategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, AffiliatesCategory $affiliatesCategory)
    {
         if ($request->hasFile('image')) 
         {

                            // dd("test");
                            $image = $request->file('image');
                            $imageName = $image->getClientOriginalName();
                            $name = time().'.'.$image->getClientOriginalExtension();
                            $destinationPath = public_path('/aggrementsdocuments');
                            $image->move($destinationPath, $imageName);

                 $updates = AffiliatesCategory::find($id);
                 $updates->acategoriesname = $request->acname;
                 $updates->affiliatespdfs = $imageName;
                 $updates->save();

                 return redirect('/affiliates-category')->with('success','Affiliates Category Updated successfully!');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AffiliatesCategory  $affiliatesCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,AffiliatesCategory $affiliatesCategory)
    {
        $deles =  AffiliatesCategory::find($id);
        $deles->delete();
        return redirect('/affiliates-category')->with('success','Affiliates Category Deleted successfully!');
    }
}
