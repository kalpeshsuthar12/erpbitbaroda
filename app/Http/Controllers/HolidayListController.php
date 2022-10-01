<?php

namespace App\Http\Controllers;

use App\HolidayList;
use Illuminate\Http\Request;

class HolidayListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hol = HolidayList::orderBy('id','DESC')->get();

        return view('superadmin.holidayslist.manage',compact('hol'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('superadmin.holidayslist.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $HolidayListmodel = new HolidayList();
        $HolidayList = $HolidayListmodel->create([
            'hdaysnames'=> $request->hdaysname,
            'hstartdates'=> $request->startdatess,
            'henddates'=> $request->enddatess,
        ]);

      

        return redirect('/holiday-lists')->with('success','Holidays List created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HolidayList  $holidayList
     * @return \Illuminate\Http\Response
     */
    public function show(HolidayList $holidayList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HolidayList  $holidayList
     * @return \Illuminate\Http\Response
     */
    public function edit($id,HolidayList $holidayList)
    {
        $eidday = HolidayList::find($id);
        return view('superadmin.holidayslist.edit',compact('eidday'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HolidayList  $holidayList
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, HolidayList $holidayList)
    {
        $upd = HolidayList::find($id);
        $upd->hdaysnames = $request->hdaysname;
        $upd->hstartdates = $request->startdatess;
        $upd->henddates = $request->enddatess;
        $upd->save();

            return redirect('/holiday-lists')->with('success','Holidays List Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HolidayList  $holidayList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,HolidayList $holidayList)
    {
        $dele = HolidayList::find($id);
         $dele->delete();
         return redirect('/holiday-lists')->with('success','Holidays List Deleted successfully!');
    }
}
