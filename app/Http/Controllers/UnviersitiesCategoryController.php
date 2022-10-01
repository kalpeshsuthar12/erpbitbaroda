<?php

namespace App\Http\Controllers;

use App\UnviersitiesCategory;
use Illuminate\Http\Request;

class UnviersitiesCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $univcater = UnviersitiesCategory::all();

        return view('superadmin.universitiescategories.manage',compact('univcater'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.universitiescategories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $UnviersitiesCategorymodel = new UnviersitiesCategory();
        $UnviersitiesCategory = $UnviersitiesCategorymodel->create([
            'unviersitiescategoriesname'=> $request->ucatname,
        ]);

        return redirect('/universities-category')->with('success','Universities Category created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UnviersitiesCategory  $unviersitiesCategory
     * @return \Illuminate\Http\Response
     */
    public function show(UnviersitiesCategory $unviersitiesCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UnviersitiesCategory  $unviersitiesCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,UnviersitiesCategory $unviersitiesCategory)
    {
        $edits = UnviersitiesCategory::find($id);
        return view('superadmin.universitiescategories.edit',compact('edits'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UnviersitiesCategory  $unviersitiesCategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, UnviersitiesCategory $unviersitiesCategory)
    {
        $updats = UnviersitiesCategory::find($id);
        $updats->unviersitiescategoriesname = $request->ucatname;
        $updats->save();
        return redirect('/universities-category')->with('success','Universities Category Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UnviersitiesCategory  $unviersitiesCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,UnviersitiesCategory $unviersitiesCategory)
    {
        $deles = UnviersitiesCategory::find($id);
        $deles->delete();
         return redirect('/universities-category')->with('success','Universities Category Deleted successfully!');
    }
}
