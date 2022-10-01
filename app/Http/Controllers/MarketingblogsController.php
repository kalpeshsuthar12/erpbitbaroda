<?php

namespace App\Http\Controllers;

use App\marketingblogs;
use App\coursesubcategory;
use App\coursecategory;
use Illuminate\Http\Request;
use DB;

class MarketingblogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogsdata =DB::table('marketingblogs')
                ->join('coursecategories', 'coursecategories.id', '=', 'marketingblogs.blogcat')
                ->join('coursesubcategories', 'coursesubcategories.id', '=', 'marketingblogs.blogsubcat')
                ->select('marketingblogs.id','coursesubcategories.subcategory_name', 'coursecategories.coursecategoryname','marketingblogs.created_at','marketingblogs.blogname','marketingblogs.blogurl')
                ->get();

        return view('superadmin.markertingmaterials.manageblogs',compact('blogsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(coursecategory $coursecategory)
    {

         $coursecat = coursecategory::get();
        return view('superadmin.markertingmaterials.createblog',compact('coursecat'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,marketingblogs $marketingblogs)
    {
        $marketingblogsmodel = new marketingblogs();
                        $marketingblogs = $marketingblogsmodel->create([
                            'blogcat'=> $request->ccategory,
                            'blogsubcat'=> $request->csubcategory,
                            'blogname'=> $request->blogname,
                            'blogurl'=> $request->burl,
                           
                        ]);

                return redirect('/blogs')->with('success','Blogs Added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\marketingblogs  $marketingblogs
     * @return \Illuminate\Http\Response
     */
    public function show(marketingblogs $marketingblogs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\marketingblogs  $marketingblogs
     * @return \Illuminate\Http\Response
     */
    public function edit($id,marketingblogs $marketingblogs,coursesubcategory $coursesubcategory,coursecategory $coursecategory)
    {
          $cat = coursecategory::all();
        $subcat = coursesubcategory::all();
        $mblogs = marketingblogs::find($id);

        return view('superadmin.markertingmaterials.editblogs',compact('cat','subcat','mblogs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\marketingblogs  $marketingblogs
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, marketingblogs $marketingblogs)
    {
                        
                        $updat = marketingblogs::find($id);
                        $updat->blogcat = $request->ccategory;
                        $updat->blogsubcat = $request->csubcategory;
                        $updat->blogname = $request->blogname;
                        $updat->blogurl = $request->burl;
                        $updat->save();

                 return redirect('/blogs')->with('success','Blogs Updated successfully!!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\marketingblogs  $marketingblogs
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,marketingblogs $marketingblogs)
    {
        $dels = marketingblogs::find($id);
        $dels->delete();
         return redirect('/blogs')->with('success','Blog Deleted successfully!!');


    }
}
