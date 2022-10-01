<?php

namespace App\Http\Controllers;

use App\QuestionsTypes;
use App\course;
use App\PracticalQuestions;
use Illuminate\Http\Request;
use Auth;

class PracticalQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $practall = PracticalQuestions::all();
        return view('superadmin.practicalquestions.manage',compact('practall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $qtypes = QuestionsTypes::all();
        $courses = course::all();

        return view('superadmin.practicalquestions.create',compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usersId = Auth::user()->id;

        $exists = PracticalQuestions::where('pqcourses',$request->courses)->where('plectures',$request->lectures)->where('pQuestions','like','%'.$request->questions.'%')->exists();

        if($exists)
        {
            return redirect()->back()->with('error','Practical Questions Already Exists !!');
        }

        else
        {

            $PracticalQuestionsmodel = new PracticalQuestions();

        $PracticalQuestions = $PracticalQuestionsmodel->create([

            'pqcourses' => $request->courses,
            'plectures' => $request->lectures,
            'pQuestions' => $request->questions,
            'pusersids' => $usersId,
        ]);

        }

        

        return redirect('/practical-questions')->with('success','Practical Exams Created Successfully!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
