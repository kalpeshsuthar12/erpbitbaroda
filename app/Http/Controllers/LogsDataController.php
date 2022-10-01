<?php

namespace App\Http\Controllers;

use App\logsData;
use Illuminate\Http\Request;

class LogsDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $uuse = logsData::all();

        return view('superadmin.useractivity.manage',compact('uuse'));
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
     * @param  \App\logsData  $logsData
     * @return \Illuminate\Http\Response
     */
    public function show(logsData $logsData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\logsData  $logsData
     * @return \Illuminate\Http\Response
     */
    public function edit(logsData $logsData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\logsData  $logsData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, logsData $logsData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\logsData  $logsData
     * @return \Illuminate\Http\Response
     */
    public function destroy(logsData $logsData)
    {
        //
    }
}
