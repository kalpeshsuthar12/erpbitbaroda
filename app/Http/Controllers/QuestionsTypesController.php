<?php

namespace App\Http\Controllers;

use App\QuestionsTypes;
use Illuminate\Http\Request;

class QuestionsTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $qall = QuestionsTypes::all();

        return view('superadmin.questionstypes.manage',compact('qall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.questionstypes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $QuestionsTypesmodel = new QuestionsTypes();
        $QuestionsTypes = $QuestionsTypesmodel->create([
            'questypenames' => $request->questionstypes,

        ]);

        return redirect('/questions-types')->with('success','Question Type Created Successfully!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuestionsTypes  $questionsTypes
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionsTypes $questionsTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\QuestionsTypes  $questionsTypes
     * @return \Illuminate\Http\Response
     */
    public function edit($id,QuestionsTypes $questionsTypes)
    {
        $edits = QuestionsTypes::find($id);

        return view('superadmin.questionstypes.edit',compact('edits'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QuestionsTypes  $questionsTypes
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, QuestionsTypes $questionsTypes)
    {
        $updates = QuestionsTypes::find($id);
        $updates->questypenames = $request->questionstypes;
        $updates->save();

        return redirect('/questions-types')->with('success','Question Type Updated Successfully!!');
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuestionsTypes  $questionsTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,QuestionsTypes $questionsTypes)
    {
         $dele = QuestionsTypes::find($id);
         $dele->delete();

         return redirect('/questions-types')->with('success','Question Type deleted Successfully!!');
    }
}
