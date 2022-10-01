<?php

namespace App\Http\Controllers;

use App\marketingimages;
use App\marketimages;
use App\coursesubcategory;
use App\coursecategory;
use App\course;
use Illuminate\Http\Request;
use DB;
class MarketingimagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imagesdata = marketingimages::all();

                return view('superadmin.markertingmaterials.managemarketingimages',compact('imagesdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(coursecategory $coursecategory)
    {
        
        $coursecat = course::get();
        return view('superadmin.markertingmaterials.createmarketingimages',compact('coursecat'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

      /*  dd($request->all());*/

      $mimage = $request->multipleimage; 
      $murl = $request->imageurl; 

      for($i=0; $i < count($mimage); $i++)
                    {
                                $marketingimages = new marketingimages([
                                
                                'coursename'   => $mimage[$i],
                                'imageurl'   => $murl,
                                
                                ]);
                            $marketingimages->save();
                    }

       

            return redirect('/marketing-images')->with('success','Images Added successfully!');     
        

          

                    
               
              

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\marketingimages  $marketingimages
     * @return \Illuminate\Http\Response
     */
    public function show($id,marketimages $marketimages)
    {
        //$imgesd = DB::select("SELECT * FROM marketimages WHERE marektingid = '".$id."'");

        $imgesd = DB::table('marketimages')->where('marektingid',$id)->get();

       return view('superadmin.markertingmaterials.viewimages',compact('imgesd'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\marketingimages  $marketingimages
     * @return \Illuminate\Http\Response
     */
    public function edit(marketingimages $marketingimages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\marketingimages  $marketingimages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, marketingimages $marketingimages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\marketingimages  $marketingimages
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,marketingimages $marketingimages)
    {
        $deletes = marketingimages::find($id);
        $deletes->delete();
        $del =  DB::table('marketimages')->where('marektingid', $id)->delete(); 

         return redirect('/marketing-images')->with('success','Images Deleted successfully!');
    }   
}
