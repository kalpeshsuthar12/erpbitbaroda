<?php

namespace App\Http\Controllers;

use App\bunchcourse;
use App\bunchcourselists;
use App\lecturereport;
use App\course;
use Illuminate\Http\Request;

class BunchcourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bunches = bunchcourse::all();
        //$le = bunchcourse::all();
        return view('superadmin.brunchcourse.manage',compact('bunches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cours = course::where('bygroup','=','By Group')->get();
        $lecte = lecturereport::all();
        return view('superadmin.brunchcourse.create',compact('cours','lecte'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
         $bunchcoursemodel = new bunchcourse();
        $bunchcourse = $bunchcoursemodel->create([
            'maincourses'=> $request->mcourse,
          
        ]);


        $bunchcoursesid = $bunchcourse->id;
    
        $subcourseslists = $request->subcourse;

         for($i=0; $i < (count($subcourseslists)); $i++)
                    {
                                $bunchcourselists = new bunchcourselists([
                                
                                'bunchmaincourseid' => $bunchcoursesid,
                                'bunchcourselists'   => $subcourseslists[$i],
                                
                            ]);
                            $bunchcourselists->save();
                    }
        return redirect('/brunch-course')->with('success','Bunch Course created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\bunchcourse  $bunchcourse
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $mains = $request->maincourses;
        $data= array();

        $result = bunchcourselists::where('bunchmaincourseid','=',$mains)->get();
        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->bunchcourselists;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\bunchcourse  $bunchcourse
     * @return \Illuminate\Http\Response
     */
    public function edit($id,bunchcourse $bunchcourse)
    {
        $cours = course::where('bygroup','=','By Group')->get();

        $editesid =  bunchcourse::find($id);
        $ldetails = bunchcourselists::where('bunchmaincourseid',$id)->get();



        return view('superadmin.brunchcourse.edit',compact('cours','editesid','ldetails'));
    }

    public function update($id,Request $request)
    {

     
                $lr = bunchcourse::find($id);
                $lr->maincourses = $request->mcourse;;
                $lr->save();

            $subcourseslists = $request->subcourse;
            $fds  = $request->fd;

            $dele = bunchcourselists::where('bunchmaincourseid',$fds)->get();
            $dele->each->delete();

           
            
           

             for($i=0; $i < (count($subcourseslists)); $i++)
                    {
                        
                         $productss = bunchcourselists::updateOrCreate(['bunchcourselists' => $subcourseslists[$i],'bunchmaincourseid' => $id ]);
                    } 
           
            

             return redirect('/brunch-course')->with('success','Bunch Course Updated successfully!');
        
    }

    /**
     * Update the specified resource in storage.
     *4
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\bunchcourse  $bunchcourse
     * @return \Illuminate\Http\Response
     */
    public function ajax($cs)
    {

        $listget = bunchcourse::where('maincourses','=',$cs)->pluck('id'); 
        $list = bunchcourselists::where('bunchmaincourseid','=',$listget)->get();

        return response()->json($list);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\bunchcourse  $bunchcourse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,bunchcourse $bunchcourse)
    {
        $del = bunchcourse::find($id);
        $dele = bunchcourselists::where('bunchmaincourseid',$id)->get();

        $del->delete();
        $dele->each->delete();

         return redirect('/brunch-course')->with('success','Bunch Course Deleted successfully!');


    }
}
