<?php

namespace App\Http\Controllers;

use App\marketingvideos;
use App\coursesubcategory;
use App\coursecategory;
use Illuminate\Http\Request;
use DB;

class MarketingvideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videosdat =DB::table('marketingvideos')
                ->join('coursecategories', 'coursecategories.id', '=', 'marketingvideos.videocat')
                ->join('coursesubcategories', 'coursesubcategories.id', '=', 'marketingvideos.videosubcat')
                ->select('marketingvideos.id','coursesubcategories.subcategory_name', 'coursecategories.coursecategoryname','marketingvideos.created_at','marketingvideos.videourl')
                ->get();

        return view('superadmin.markertingmaterials.managevideos',compact('videosdat'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(coursecategory $coursecategory)
    {
        $coursecat = coursecategory::get();
        return view('superadmin.markertingmaterials.createmarketingvideo',compact('coursecat'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,marketingvideos $marketingvideos)
    {
        $marketingvideosmodel = new marketingvideos();
                        $marketingvideos = $marketingvideosmodel->create([
                            'videocat'=> $request->ccategory,
                            'videosubcat'=> $request->csubcategory,
                            'videourl'=> $request->vurl,
                            ]);

                return redirect('/marketing-videos')->with('success','Video Added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\marketingvideos  $marketingvideos
     * @return \Illuminate\Http\Response
     */
    public function show(marketingvideos $marketingvideos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\marketingvideos  $marketingvideos
     * @return \Illuminate\Http\Response
     */
    public function edit($id,marketingvideos $marketingvideos,coursesubcategory $coursesubcategory,coursecategory $coursecategory)
    {
         $cat = coursecategory::all();
        $subcat = coursesubcategory::all();
        $mvideos = marketingvideos::find($id);

        return view('superadmin.markertingmaterials.editmarketingvideos',compact('cat','subcat','mvideos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\marketingvideos  $marketingvideos
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, marketingvideos $marketingvideos)
    {
        $updsa = marketingvideos::find($id);
        $updsa->videocat = $request->ccategory;
        $updsa->videosubcat = $request->csubcategory;
        $updsa->videourl = $request->vurl;
        $updsa->save();

        return redirect('/marketing-videos')->with('success','Video Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\marketingvideos  $marketingvideos
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,marketingvideos $marketingvideos)
    {
        $dele = marketingvideos::find($id);
        $dele->delete();
        return redirect('/marketing-videos')->with('success','Video Deleted successfully!');
    }
}
