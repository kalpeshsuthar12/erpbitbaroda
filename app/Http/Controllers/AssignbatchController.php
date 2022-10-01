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
use App\Batchs_logs;
use App\BatchCourseDurations;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AssignbatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$ab = DB::select('SELECT * FROM admissionprocesses a, admissionprocesscourses c,payments p WHERE a.id = p.inviceid AND a.id = c.invid');
        $ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->where('admissionprocesses.batchsstatus',NULL)->orderBy('admissionprocesses.id','DESC')->get();

        $cour = course::all();
       

        return view('superadmin.assignbatch.manage',compact('ab','cour'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $alldatsa = $request->mnps;

        //$coursedata = payment::whereIn('id',$alldatsa)->pluck('inviceid');
        
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
         $brans = Branch::all();
        return view('superadmin.assignbatch.create',compact('studentbatvhs','csa','da','tmes','use','brans'));
    }


    public function editbatchdetails($id)
    {
        $getbatch = assignbatch::find($id);

            $getadmiofbatchs = assignbatchesdetails::join('users','users.ustusdentsadmssionsids','=','assignbatchesdetails.stusdentsadmssionsids')->where('assignbatchesdetails.assignbatchid',$id)->select('assignbatchesdetails.*','users.*','users.id as uid')->get();
            //dd($getadmiofbatchs);


                foreach($getadmiofbatchs as $batchwisead)
                {
                    $admisId = $batchwisead->stusdentsadmssionsids;

                     $course = admissionprocesscourses::join('courses','courses.id','=','admissionprocesscourses.courseid')->where('invid',$batchwisead->stusdentsadmssionsids)->select('courses.id','courses.coursename')->get();
                     $getselecrtedcourse = assignbatchesdetails::join('courses','courses.id','=','assignbatchesdetails.course')->where('stusdentsadmssionsids',$batchwisead->stusdentsadmssionsids)->first();
                   
                }
                    //dd($getadmiofbatchs);
               // $studentbatvhs = admissionprocess::whereIn('id',$admisId)->get();

                     /*if($studentbatvhs){
                        foreach($studentbatvhs as $std){
                            $csa = admissionprocesscourses::join('courses','admissionprocesscourses.courseid','=','courses.id')->where('invid',$std->id)->get();
                            $std->csa = $csa;
                        }
                    }*/

                     $da = days::all();
                     $tmes = times::all();
                     $use = User::where('usercategory','=','Instructor')->get();
                     $brans = Branch::all();
                     //$course = BatchCourseDurations::where('csa')->get();
                     $coursedurations = BatchCourseDurations::where('batcourids',$batchwisead->course)->get();
                    return view('superadmin.assignbatch.edit',compact('getadmiofbatchs','da','tmes','use','brans','getbatch','coursedurations','course','getselecrtedcourse'));
           
    }

    public function updatebatches($id,Request $request)
    {
        $edits = assignbatch::find($id);

            $usersid = $request->studentsadmissionsids;
            $userdele = User::whereIn('ustusdentsadmssionsids',$usersid)->get();
            //dd($userdele);

            $admissionsids = assignbatchesdetails::where('assignbatchid',$id)->get();




            $admissionsids->each->delete();
            $userdele->each->delete();



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
      /*  $assignbatchmodel = new assignbatch();
        $assignbatch = $assignbatchmodel->create([
            'faculty'=> $request->facutys,
            'bdurationsdays' => $request->bdurations,
            'startdate'=> $request->stardate,
            'enddate'=> $request->enddate,
            'jointo'=> $request->joinsto,
            'batchtimes'=> $batimes,
            'days'=> $fcurses,
            'classurls'=> $request->curls,
            'assignstatus'=> '1',
        ]);*/

        $edits->faculty = $request->facutys;
        $edits->bdurationsdays = $request->bdurations;
        $edits->startdate = $request->stardate;
        $edits->enddate = $request->enddate;
        $edits->jointo =  $request->joinsto;
        $edits->batchtimes = $batimes;
        $edits->days = $request->days;
        $edits->classurls = $request->curls;
        $edits->save(); 
       

        $assignbatchid = $id;

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
        $studentsname = $request->studentsname;
        


        for($i=0; $i < (count($studentses)); $i++)
                    {
                                $assignbatchesdetails = new assignbatchesdetails([
                                
                                'assignbatchid' => $assignbatchid,
                                'students'   => $studentsname[$i],
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
                                
                                'name'   => $studentsname[$j],
                                'email'   => $gusernames[$j],
                                'ustusdentsadmssionsids'   => $saids[$j],
                                'password'   =>  Hash::make($gpasswords[$j]),
                                'sepass'   =>  $gpasswords[$j],
                                'usercategory'   => "Student",
                                ]);
                            $userdetails->save();
                    }


                    return redirect('/batch-details')->with('success','Batch updated Successfully!!');

    }

    public function deletebatchdetails($id)
    {
        $delebatchs = assignbatch::find($id);

        $deletegetid = assignbatchesdetails::where('assignbatchid',$id)->get();
        foreach($deletegetid as $delete)
        {
            $userdeles = User::where('ustusdentsadmssionsids',$delete->stusdentsadmssionsids)->get();
            $userdeles->each->delete();
        }

        $deletegetid->each->delete();
        $delebatchs->delete();
        

        return redirect('/batch-details')->with('success','Batch Successfully Deleted!!');

                
        //$delebatchdetails = assignbatchesdetails::whereIn()->get();
    }

    public function getbatchtransferdetails(Request $request)
    {
            $btachids = $request->batchid;

            $getadmiofbatchs = assignbatchesdetails::where('assignbatchid',$btachids)->get();
             //dd($getadmiofbatchs);
            foreach ($getadmiofbatchs as $value) 
            {   


                //dd($value->stusdentsadmssionsids);
                $studentbatvhs = admissionprocess::where('id',$value->stusdentsadmssionsids)->get();

                                if($studentbatvhs){
                            foreach($studentbatvhs as $std){
                                $csa = admissionprocesscourses::join('courses','admissionprocesscourses.courseid','=','courses.id')->where('invid',$std->id)->get();
                                $std->csa = $csa;
                            }
                        }


                
            }

            $getusesrsid = assignbatch::where('id',$btachids)->latest()->first();
            $fusersid = $getusesrsid->faculty;
           
            
            //dd($fusersid);
            $getcoursedet = assignbatchesdetails::where('assignbatchid',$btachids)->groupBy('assignbatchid')->get();

                foreach($getcoursedet as $csid)
                {
                    $getcourse = course::find($csid->course);

                }

            //dd($getcourse);

             $da = days::all();
         $tmes = times::all();
         $use = User::where('id','!=',$fusersid)->where('usercategory','=','Instructor')->whereRaw('FIND_IN_SET("'.$getcourse->coursename.'",facultycourse)')->get();
         $brans = Branch::all();
        return view('superadmin.assignbatch.batchtransferdetails',compact('studentbatvhs','csa','da','tmes','use','brans','getadmiofbatchs','fusersid','btachids','getusesrsid'));

    }


    public function storebatchtransferdetails(Request $request)
    {
            $Batchs_logsmodel = new Batchs_logs();

            $Batchs_logs = $Batchs_logsmodel->create([
            'batchesids'=> $request->btachesids,
            'atransferfrom'=> $request->tfroms,
            'atransferto'=> $request->facutys,
            'abatctime'=> $request->batchtime,
            'adays'=> $request->btchdays,
            'ajointos'=> $request->btchjointo,
            'aclassurls'=> $request->btchclasurl,
            'astartdate'=> $request->btchstartdates,
            'aenddate'=> $request->btchenddates,
            'abatchdurations'=> $request->bdurations,
            'atransfdates'=> $request->transfdates,
           
        ]);


            $updates  = assignbatch::find($request->btachesids);
            $updates->faculty = $request->facutys;
            $updates->ftransferfroms = $request->tfroms;
            $updates->days = $request->days;
            $updates->batchtimes = $request->batchtimes;
            $updates->jointo = $request->joinsto;
            $updates->startdate = $request->stardate;
            $updates->enddate = $request->enddate;
            $updates->bdurationsdays = $request->bdurations;
            $updates->transfdates = $request->transfdates;
            $updates->save();

            return redirect('/transfer-batches-lists')->with('success','Batch Transfer successfully!!');
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
            'bdurationsdays' => $request->bdurations,
            'startdate'=> $request->stardate,
            'enddate'=> $request->enddate,
            'jointo'=> $request->joinsto,
            'batchtimes'=> $batimes,
            'days'=> $request->days,
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

        return redirect('/batch-details')->with('success','Batch created successfully!');
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
          
                 $studentsadmissions = admissionprocess::join('payments','payments.inviceid','=','admissionprocesses.id')->join('assignbatchesdetails','assignbatchesdetails.stusdentsadmssionsids','=','admissionprocesses.id')->where('assignbatchesdetails.assignbatchid',$id)->select('assignbatchesdetails.*','admissionprocesses.*','payments.*','admissionprocesses.id as aid')->groupBy('payments.inviceid')->get();

                 $batchsdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->get();
        

       
        

        return view('superadmin.assignbatch.studentsrecords',compact('studentsadmissions','batchsdetails'));

    }   

    public function batchsdetails(Request $request)
    {   

        $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

        $useall = User::where('usercategory','=','Instructor')->get();
        $timedata = times::all();

        return view('superadmin.assignbatch.batchsdetails',compact('bdetails','useall','timedata'));

    }

    public function transferbatchslists()
    {
         $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->where('assignbatches.ftransferfroms','!=',null)->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

        $useall = User::where('usercategory','=','Instructor')->get();
        $timedata = times::all();

        return view('superadmin.assignbatch.transferbatchdetails',compact('bdetails','useall','timedata'));
    }

    public function transferlogs($id)
    {
        $bdetails = assignbatch::join('batchs_logs','batchs_logs.batchesids','=','assignbatches.id')->orderBy('assignbatches.id','DESC')->select('batchs_logs.*','assignbatches.*','assignbatches.id as batchids')->get();

        return view('superadmin.assignbatch.transferlogs',compact('bdetails'));

       
    }




    public function filterbatches(Request $request)
    {
            $instuctos = $request->inststructorid;
            $tms = $request->times;

             if(!empty($instuctos))
            {

               // dd('text');
                 $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->where('assignbatches.faculty',$instuctos)->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

                        $useall = User::where('usercategory','=','Instructor')->get();
                        $timedata = times::all();

                        return view('superadmin.assignbatch.filterbatches',compact('bdetails','useall','timedata','tms','instuctos'));

            }

            if(!empty($tms))
            {
                 $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->where('assignbatches.batchtimes',$tms)->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

                        $useall = User::where('usercategory','=','Instructor')->get();
                        $timedata = times::all();

                        return view('superadmin.assignbatch.filterbatches',compact('bdetails','useall','timedata','tms','instuctos'));

            }

            if($instuctos > 0  && $tms > 0)
            {
                        $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->where('assignbatches.faculty',$instuctos)->where('assignbatches.batchtimes',$tms)->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

                        $useall = User::where('usercategory','=','Instructor')->get();
                        $timedata = times::all();

                        return view('superadmin.assignbatch.filterbatches',compact('bdetails','useall','timedata','tms','instuctos'));

            }

           

        
    }


    public function allovereports($id)
    {
        //$bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

        $bdetails = assignbatch::find($id);

        return view('superadmin.assignbatch.overallreports',compact('bdetails'));
    }

    public function attendancereports($id)
    {
        //$bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

        //$bdetails = assignbatch::find($id);

        //$bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->get();

        $bdetails  = assignbatchesdetails::join('lecturereports','lecturereports.courses','=','assignbatchesdetails.course')->join('assignbatches','assignbatches.id','=','assignbatchesdetails.assignbatchid')->select('assignbatchesdetails.*','lecturereports.*','assignbatches.*','assignbatches.id as batchids','lecturereports.id as lid','assignbatchesdetails.id as abdid')->groupBy('assignbatchesdetails.assignbatchid')->get();

        return view('superadmin.assignbatch.attendancedetails',compact('bdetails','id'));
    }


    public function show(assignbatch $assignbatch)
    {

            $asbatch = assignbatch::all();
            //dd($asbatch);

            return view('superadmin.assignbatch.assignbatches',compact('asbatch'));
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

           // dd($cuniversities->byuniversitites);

            if($cuniversities->byuniversitites == "BIT Institute")
            {
                $ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->where('admissionprocesscourses.courseid',$courseid)->orderBy('admissionprocesses.id','DESC')->where('admissionprocesses.batchsstatus',NULL)->get();

                        
                        $cour = course::all();


                       

                        return view('superadmin.assignbatch.filterpendingbatches',compact('ab','cour','cuniversities'));
            }

            else
            {   

                 $ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->where('admissionprocesses.batchsstatus',NULL)->where('admissionprocesscourses.univecoursid',$courseid)->orderBy('admissionprocesses.id','DESC')->get();

                        $cour = course::all();
                       

                        return view('superadmin.assignbatch.filterpendingbatches',compact('ab','cour','cuniversities'));

            }


        /*$ab = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as csids')->orderBy('admissionprocesses.id','DESC')->get();

        $cour = course::all();
       

        return view('superadmin.assignbatch.manage',compact('ab','cour'));*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\assignbatch  $assignbatch
     * @return \Illuminate\Http\Response
     */
    public function destroy($coursespeciawl,assignbatch $assignbatch)
    {   

        //dd($allfaculty);

        $coursedatas =  course::find($coursespeciawl);

         $allfaculty = User::whereRaw('FIND_IN_SET("'.$coursedatas->coursename.'",facultycourse)')->get();

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

        return view('superadmin.assignbatch.assignnewbatch',compact('branchdata','coursedata','fa','da','tes','ab'));
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

    public function certificatelists()
    {
        return view('superadmin.assignbatch.certificateslists');
    }


    public function instructorsbook($instructors,$bathsdays,$btaimts)
    {

       if($em = assignbatch::where('faculty','=',$instructors)->where('batchtimes','=',$btaimts)->where('days','=',$bathsdays)->first())
            {
                          return response()->json(
                                [
                                    'success' => true,
                                    'message' => 'Faculty Already Have Batch !!'
                                ]);
            }

            else
            {
                 $msg = " ";
                    return response()->json($msg);
            }
    }

    public function getstudetlists(Request $request)
    {
        $studnelist = $request->getstudentsnamw;
        //   dd($studnelist);
        

    }
    
    public function batchnos($branchs)
    {
         $year = date("Y");
         $month = date("m");
        
        if($branchs == "BITSJ")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = assignbatch::where('jointo','=',$branchs)->latest()->get()->pluck('bsjno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BATCH-BITSJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }

        else if($branchs == "BITMJ")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = assignbatch::where('jointo','=',$branchs)->latest()->get()->pluck('bmjno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BATCH-BITMJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }


        else if($branchs == "BITWG")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = assignbatch::where('jointo','=',$branchs)->latest()->get()->pluck('bwgno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BATCH-BITWG/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }


         else if($branchs == "BITEL")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = assignbatch::where('jointo','=',$branchs)->latest()->get()->pluck('belno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BATCH-BITEL/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }
    }
}
