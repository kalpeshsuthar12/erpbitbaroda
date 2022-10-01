<?php

namespace App\Http\Controllers;

use App\bannedleads;
use Auth;
use Illuminate\Http\Request;

class BannedleadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $bannesdata = bannedleads::where('busersid',$userId)->get();

        return view('marketing.banneddata.manage',compact('bannesdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\bannedleads  $bannedleads
     * @return \Illuminate\Http\Response
     */
    public function show(bannedleads $bannedleads)
    {
         $userId = Auth::user()->id;
        $bannesdata = bannedleads::where('busersid',$userId)->get();

        return view('affiliatesmarketing.banneddata.manage',compact('bannesdata'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\bannedleads  $bannedleads
     * @return \Illuminate\Http\Response
     */
    public function edit(bannedleads $bannedleads)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\bannedleads  $bannedleads
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, bannedleads $bannedleads)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\bannedleads  $bannedleads
     * @return \Illuminate\Http\Response
     */
    public function destroy(bannedleads $bannedleads)
    {
        //
    }
}
