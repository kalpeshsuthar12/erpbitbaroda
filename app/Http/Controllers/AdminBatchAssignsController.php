<?php

namespace App\Http\Controllers;
use App\times;
use App\days;
use App\assignbatch;
use App\assignbatchesdetails;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\payment;
use App\Branch;
use App\course;
use App\User;
use DB;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminBatchAssignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$ab = DB::select('SELECT * FROM admissionprocesses a, admissionprocesscourses c,payments p WHERE a.id = p.inviceid AND a.id = c.invid');


        $userbranchs = Auth::user()->branchs;
        $ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->where('admissionprocesses.stobranches',$userbranchs)->orderBy('admissionprocesses.id','DESC')->get();
        $cour = course::all();
       

        return view('admin.assignbatch.manage',compact('ab','cour'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $alldatsa = $request->mnps;

        $userbranchs = Auth::user()->branchs;

        //$coursedata = payment::whereIn('id',$alldatsa)->pluck('inviceid');
          //$userbranchs = Auth::user()->branchs;
        $studentbatvhs = admissionprocess::whereIn('id',$alldatsa)->get();
        if($studentbatvhs){
            foreach($studentbatvhs as $std){
                $csa = admissionprocesscourses::join('courses','admissionprocesscourses.courseid','=','courses.id')->where('invid',$std->id)->get();
                $std->csa = $csa;
            }
        }

         $da = days::all();
         $tmes = times::all();
         $use = User::where('usercategory','=','Instructor')->get();
         $brans = Branch::where('branchname',$userbranchs)->get();
        return view('admin.assignbatch.create',compact('studentbatvhs','csa','da','tmes','use','brans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         $faculcourse = $request->days;
         $btimes = $request->batchtimes;


        if(is_array($faculcourse)) 
                 {
                    
                  
                     $fcurses  = implode(',',$faculcourse);

                  }

                  else
                  {
                    $fcurses = $request->days;

                  }



                  if(is_array($btimes)) 
                 {
                    
                  
                     $batimes  = implode(',',$btimes);

                  }

                  else
                  {
                    $batimes = $request->batchtimes;

                  }
        $assignbatchmodel = new assignbatch();
        $assignbatch = $assignbatchmodel->create([
            'faculty'=> $request->facutys,
            'branches'=> $request->jointo,
            'startdate'=> $request->stardate,
            'enddate'=> $request->enddate,
            'jointo'=> $request->joinsto,
            'batchtimes'=> $batimes,
            'days'=> $fcurses,
            'classurls'=> $request->curls,
            'assignstatus'=> '1',
        ]);
        $assignbatchid = $assignbatch->id;

        $studentses = $request->students;
        $gusernames = $request->gusernames;
        $gpasswords = $request->gpasswords;
      
        $enrollments = $request->ernos;
        $brancg = $request->branches;
        $moviles = $request->mobiles;
        $studntcoursres = $request->assignbatchcourse;
        $asdssubcourses = $request->subcourses;
        $aspecializations = $request->specializations;
        $saids = $request->studentsadmissionsids;
        $cmodess = $request->cmodes;
        


        for($i=0; $i < (count($studentses)); $i++)
                    {
                                $assignbatchesdetails = new assignbatchesdetails([
                                
                                'assignbatchid' => $assignbatchid,
                                'students'   => $studentses[$i],
                                'mode'   => $studentses[$i],
                                'gsuiteusernames'   => $gusernames[$i],
                                'gpasswords'   =>  Hash::make($gpasswords[$i]),
                                'guspasswords'   =>$gpasswords[$i],
                                'course'   =>$studntcoursres[$i],
                                'subcourse'   => $asdssubcourses[$i],
                                'specializationsss'   => $aspecializations[$i],
                                'stusdentsadmssionsids' => $saids[$i],
                                'scoursmode' => $cmodess[$i],
                                
                            ]);
                            $assignbatchesdetails->save();
                    }


                    for($j=0; $j < (count($studentses)); $j++)
                    {
                                $userdetails = new User([
                                
                                'name'   => $studentses[$j],
                                'email'   => $gusernames[$j],
                                'ustusdentsadmssionsids'   => $saids[$j],
                                'password'   =>  Hash::make($gpasswords[$j]),
                                'sepass'   =>  $gpasswords[$j],
                                'usercategory'   => "Student",
                                ]);
                            $userdetails->save();
                    }


        $getstudentss = $assignbatchesdetails->students;
        
        $udau = admissionprocess::whereIn("id",$saids)->update(['batchsstatus' => 1]);
 

        $updatesdat = admissionprocess::where('studentname',$getstudentss)->first();
        $updatesdat->admissionstatus = "Old Student";
        $updatesdat->save();

       $updatecoursede = admissionprocess::where('studentname',$getstudentss)->pluck('id');
     //  dd($updatecoursede);
        $updaecoursedata = admissionprocesscourses::where('invid',$updatecoursede)->first();

        //dd($updaecoursedata);
        $updaecoursedata->studentsin = "Old Student"; 
        $updaecoursedata->save();

        $updatepayments = payment::where('inviceid',$updatecoursede)->first();
        $updatepayments->studentadmissiionstatus = "Old Student";
        $updatepayments->save();

        return redirect('/admins-batch-details')->with('success','Batch Schedule successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\assignbatch  $assignbatch
     * @return \Illuminate\Http\Response
     */


    public function studentsrecordsbatches($id)
    {   

           $adetails =  assignbatchesdetails::where('assignbatchid',$id)->get();
/*
           foreach($adetails as $nadetails)
           {
                $fdetails = $nadetails->stusdentsadmssionsids;
           }*/
          //dd($fdetails);

             $userbranchs = Auth::user()->branchs;
          
                 $studentsadmissions = admissionprocess::join('payments','payments.inviceid','=','admissionprocesses.id')->join('assignbatchesdetails','assignbatchesdetails.stusdentsadmssionsids','=','admissionprocesses.id')->where('admissionprocesses.stobranches',$userbranchs)->where('assignbatchesdetails.assignbatchid',$id)->select('assignbatchesdetails.*','admissionprocesses.*','payments.*','admissionprocesses.id as aid')->groupBy('payments.inviceid')->get();

                 $batchsdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatches.jointo',$userbranchs)->where('assignbatchesdetails.assignbatchid',$id)->get();
        

       
        

        return view('admin.assignbatch.studentsrecords',compact('studentsadmissions','batchsdetails'));

    }   

    public function batchsdetails(Request $request)
    {   

        $userbranchs = Auth::user()->branchs;

        $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->where('assignbatches.jointo',$userbranchs)->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

        return view('admin.assignbatch.batchsdetails',compact('bdetails'));

    }


    public function show(assignbatch $assignbatch)
    {

            $asbatch = assignbatch::all();
            //dd($asbatch);

            return view('admin.assignbatch.assignbatches',compact('asbatch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\assignbatch  $assignbatch
     * @return \Illuminate\Http\Response
     */
    public function edit($courseval)
    {
        //$getcourses = course::where('coursename',$courseval)->pluck('id');
        $getca = coursebunchlist::where('courseid',$courseval)->get();
       /* $getca = coursespecializationlist::where('coursessid',$getcourses)->get();*/
         return response()->json($getca);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\assignbatch  $assignbatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, assignbatch $assignbatch)
    {
        
            $courseid = $request->coursedatas;

            $cuniversities = course::find($courseid);

             $userbranchs = Auth::user()->branchs;


           // dd($cuniversities->byuniversitites);

            if($cuniversities->byuniversitites == "BIT Institute")
            {
                $ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->where('admissionprocesscourses.courseid',$courseid)->where('admissionprocesses.stobranches',$userbranchs)->orderBy('admissionprocesses.id','DESC')->get();

                        
                        $cour = course::all();


                       

                        return view('admin.assignbatch.filterpendingbatches',compact('ab','cour','cuniversities'));
            }

            else
            {   

                 $ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->where('admissionprocesscourses.univecoursid',$courseid)->where('admissionprocesses.stobranches',$userbranchs)->orderBy('admissionprocesses.id','DESC')->get();

                        $cour = course::all();
                       

                        return view('admin.assignbatch.filterpendingbatches',compact('ab','cour','cuniversities'));

            }


        /*$ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->orderBy('admissionprocesses.id','DESC')->get();

        $cour = course::all();
       

        return view('admin.assignbatch.manage',compact('ab','cour'));*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\assignbatch  $assignbatch
     * @return \Illuminate\Http\Response
     */
    public function destroy($coursespeciawl,assignbatch $assignbatch)
    {
         $allfaculty = User::whereIn('facultycourse',$coursespeciawl)->get();
        //dd($allfaculty);

        return json_encode($allfaculty);
    }
    public function assignnewbatch(assignbatch $assignbatch)
    {
        
         $branchdata = branch::get();
         $coursedata = course::get();
         $fa = User::where('usercategory','=','Teacher')->get();
         $da = days::all();
         $tes = times::all();
         $ab = DB::select('SELECT * FROM admissionprocesses a, payments p WHERE a.admissionstatus = "New Student" AND p.studentadmissiionstatus ="New Student" AND  a.id = p.inviceid');

        return view('admin.assignbatch.assignnewbatch',compact('branchdata','coursedata','fa','da','tes','ab'));
    }

    public function getfaculty($scourse)
    {
        $allfaculty = User::where('facultycourse','like','%'.$scourse.'%')->get();
        //dd($allfaculty);

        return json_encode($allfaculty);

    }


    public function specilaization($courseval)
    {
         //$getcoursesid = course::where('coursename',$courseval)->pluck('id');
         $getspecia = coursespecializationlist::where('coursessid',$courseval)->get();

        //ddddd dd($getspecia);
         return response()->json($getspecia);
    }

    public function getstudetlists(Request $request)
    {
        $studnelist = $request->getstudentsnamw;
        //   dd($studnelist);
        

    }
}