<?php

namespace App\Http\Controllers;
use App\QuestionsTypes;
use App\course;
use App\lecturereport;
use App\lecturereportsdetails;
use App\Questions;
use App\QuestionswithAnswers;
use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Auth;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getallquest = Questions::all();

        return view('superadmin.questions.manage',compact('getallquest'));
        
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

        return view('superadmin.questions.create',compact('courses'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $usersId = Auth::user()->id;

        $Questionsmodel = new Questions();
        $Questions = $Questionsmodel->create([

            'qcourseid' => $request->courses,
            'qlectures' => $request->lectures,
            'qquestions' => $request->questions,
            'qusersids' => $usersId,
            'aoptions' => $request->aoptions,
            'boptions' => $request->boptions,
            'coptions' => $request->coptions,
            'doptions' => $request->doptions,
            'correctanswers' => $request->canswers,

        ]);


        return redirect('/questions')->with('success','Questions Created Sucessfully!!');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('superadmin.questions.importquestions');
    }


    public function importquestions(Request $request)
    {
        Excel::import(new QuestionsImport,request()->file('file'));
           
        return redirect('/questions')->with('success','Questions Imported Successfully !!');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edits = Questions::find($id);

        return view('superadmin.questions.edit',compact('edits'));
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

        $aprocess = Questions::find($id);
        
         $image = $request->file('image');
             $imagedata = file_get_contents($image);
            $base64 = base64_encode($imagedata);

             $aprocess->qimgs = $base64;
                  $aprocess->save();

                  return redirect('/questions')->with('success','Questions Images Is Uploaded Successfully!!');
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

    public function ajax($cours)
    {
        //$getcourse = course::find($cours);
        $getlectid = lecturereport::where('courses',$cours)->first();
        $gettotallect = lecturereportsdetails::where('lectureid',$getlectid->id)->get();

        return response()->json($gettotallect);
    }

    public function leccoursewajax($cours ,$getlec,$questions)
    {

        $getdetails = Questions::where('qcourseid',$cours)->where('qlectures',$getlec)->where('qquestions','like','%'.$questions.'%')->exists();
        if($getdetails)
        {

            return response()->json(
            [
                'success' => true,
                'message' => 'Questions Already Exist!!'
            ]);
        }

        else
        {
             $mesg = " ";
             return response()->json($mesg);
        }
    }
}
