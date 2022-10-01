<?php
namespace App\Http\Controllers;
use App\Branch;
use App\course;
use App\coursecategory;
use App\coursesubcategory;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\lecturereport;
use App\UnviersitiesCategory;
use App\universititiesfeeslist;
use App\Tax;
use DB;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
             
                
                $coursedata =DB::table('courses')
                ->join('coursecategories', 'coursecategories.id', '=', 'courses.cat_id')
                ->join('coursesubcategories', 'coursesubcategories.id', '=', 'courses.subcat_id')
                ->select('courses.id','coursesubcategories.subcategory_name', 'coursecategories.coursecategoryname','courses.created_at','courses.coursename','courses.courseprice','courses.courseonlineprice','courses.website','courses.leadslimitations','courses.coursedurations','courses.bygroup','courses.branches')
                ->get();

                

            return view('superadmin.course.manage',compact('coursedata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(coursecategory $coursecategory,coursesubcategory $coursesubcategory,Tax $tax)
    {
        //


        $taxes = $tax::get();
        $coursecat = $coursecategory::get();
        $branchss = Branch::get();
         $ucats = UnviersitiesCategory::all();

       
        //$coursesubcat = $coursecategory::get();

        return view('superadmin.course.create',compact('taxes','coursecat','branchss','ucats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,course $course)
    {



         $subcourse = $request->subcourse;
                                 $specialia = $request->specializations;

                                 $specilailecut = $request->lessturereports;
                                 $subclectreporst = $request->buncgaelecturerpoerst;
                                 $universiname = $request->univername;
                                 $universifees = $request->univfees;
                                 $bitdfrsss = $request->bitfees;
                                 $trotdfeels = $request->totalfees;
                                 $civsibles = $request->visible;
                                 $byunitsd = $request->universities;


                                 $adfor =  $request->admissionfor;
                                 $anewuni =  $request->newunivFeees;
                                 $abitfe =  $request->bitfessess;
                                 $aoveral =  $request->overallfees;
                              
                                
           






                        if ($request->hasFile('image')) 
                        {

                            // dd("test");
                            $image = $request->file('image');
                            $imageName = $image->getClientOriginalName();
                            $name = time().'.'.$image->getClientOriginalExtension();
                            $destinationPath = "public_path('/brocheure')";
                            $image->move($destinationPath, $imageName);
                            








                            $branchses = $request->branchses;
                            if(is_array($branchses)) 
                             {
                                
                              
                                 $brans  = implode(',',$branchses);

                              }

                              else
                              {
                                $brans = $request->branchses;

                              }
                            





                        $coursemodel = new course();
                        $courses = $coursemodel->create([
                            'cat_id'=> $request->ccategory,
                            'subcat_id'=> $request->csubcategory,
                            'coursename'=> $request->coursename,
                            'bygroup'=> $request->bygroup,
                            'byspecialization'=> $request->byspecification,
                            'courseprice'=> $request->courseprice,
                            'courseonlineprice'=> $request->onlinecourseprice,
                            'coursetax'=> $request->taxe,
                            'leadslimitations'=> $request->leadduration,
                            'coursedurations'=> $request->courseduration,
                            'brocheurefiles'=> $imageName,
                            'website'=> $request->websites,
                            'branches' => $brans,
                            'byuniversitites'=> $byunitsd,
                            'bitfees'=> $bitdfrsss,
                            'totalfees' =>$trotdfeels,
                            'coursevisible'=> $civsibles,
                            'mimgurl'=> $request->miurl,
                            'markvidurl'=> $request->mvurl,
                            'smateriaurl'=> $request->smurl,
                           
                        ]);


                        $cid = $courses->id;
                                  
                                    

                                   /* $dsd = $request->fd;
                                    $spdel = $request->fsd;*/




                                if($subcourse != 0)
                                {
                                    for($i=0; $i < (count($subcourse)); $i++)
                                        {
                                            
                                             $dakmsm = coursebunchlist::updateOrCreate(['bunchcourselists' => $subcourse[$i],'courseid' => $cid,'bunlecturereports' => $subclectreporst[$i] ]);

                                          
                  


                                        }
                                }
                                if($adfor != 0)
                                {
                                    for($k=0; $k < (count($adfor)); $k++)

                                    {
                                        $newdataforuniversitiesfesslist = universititiesfeeslist::updateOrCreate(['coursid' => $cid,'universitiesfor' => $adfor[$k], 'univfees' => $anewuni[$k] ,'bifees' => $abitfe[$k],'overallfees' => $aoveral[$k]]);
                                    }
                                }

                                if ($specialia != 0) 
                                {
                                    for($j=0; $j < (count($specialia)); $j++)
                                    {
                                    
                                     $productss = coursespecializationlist::updateOrCreate(['cspecializationslists' => $specialia[$j],'coursessid' => $cid, 'cspecialilecturereportlis' => $specilailecut[$j] ]);
                                    }  
                                }

                            return redirect('/course')->with('success','Course Created successfully!');
                        }
                    else
                    {

                            $branchses = $request->branchses;
                            if(is_array($branchses)) 
                             {
                                
                              
                                 $brans  = implode(',',$branchses);

                              }

                              else
                              {
                                $brans = $request->branchses;

                              }
                           
                       $coursemodel = new course();
                        $courses = $coursemodel->create([
                            'cat_id'=> $request->ccategory,
                            'subcat_id'=> $request->csubcategory,
                            'coursename'=> $request->coursename,
                            'bygroup'=> $request->bygroup,
                            'byspecialization'=> $request->byspecification,
                            'courseprice'=> $request->courseprice,
                            'courseonlineprice'=> $request->onlinecourseprice,
                            'coursetax'=> $request->taxe,
                            'leadslimitations'=> $request->leadduration,
                            'coursedurations'=> $request->courseduration,
                            'brocheurefiles'=> $imageName,
                            'website'=> $request->websites,
                            'branches' => $brans,
                            'byuniversitites'=> $byunitsd,
                            'bitfees'=> $bitdfrsss,
                            'totalfees' =>$trotdfeels,
                            'coursevisible'=> $civsibles,
                            'mimgurl'=> $request->miurl,
                            'markvidurl'=> $request->mvurl,
                            'smateriaurl'=> $request->smurl,
                           
                        ]);
                         $cid = $courses->id;

                                    
                                    $dsd = $request->fd;
                                    $spdel = $request->fsd;

                                    
                                if($subcourse != 0)
                                {
                                    for($i=0; $i < (count($subcourse)); $i++)
                                        {
                                            
                                             $dakmsm = coursebunchlist::updateOrCreate(['bunchcourselists' => $subcourse[$i],'courseid' => $cid,'bunlecturereports' => $subclectreporst[$i] ]);


                                        }
                                }
                                

                                if ($specialia != 0) 
                                {
                                    for($j=0; $j < (count($specialia)); $j++)
                                    {
                                    
                                    $productss = coursespecializationlist::updateOrCreate(['cspecializationslists' => $specialia[$j],'coursessid' => $cid, 'cspecialilecturereportlis' => $specilailecut[$j] ]);
                                    }  
                                }


                                if($adfor != 0)
                                {
                                    for($k=0; $k < (count($adfor)); $k++)

                                    {
                                        $newdataforuniversitiesfesslist = universititiesfeeslist::updateOrCreate(['coursid' => $cid,'universitiesfor' => $adfor[$k], 'univfees' => $anewuni[$k] ,'bifees' => $abitfe[$k],'overallfees' => $aoveral[$k]]);
                                    }
                                }
                                 

                                
                        
                   
                                 return redirect('/course')->with('success','Course Created successfully!');
                     
                    }


          
    }

     public function ajax($id,Request $request)
    {
        //

         /*$msg = "This is a simple message.";
      return response()->json(array('msg'=> $msg), 200);*/
        $subcategory = DB::table("coursesubcategories")
                    ->select("subcategory_name","id")
                    ->where("coursecat_id",$id)
                    ->get();
                    
        /*$subcategory = DB::select('SELECT id,subcategory_name FROM coursesubcategories WHERE coursecat_id = "$id"');*/
        return json_encode($subcategory);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit($id,course $course,coursesubcategory $coursesubcategory,coursecategory $coursecategory,Tax $tax)
    {
        //

        $cat = $coursecategory::all();
        $subcat = $coursesubcategory::all();
        $taxs  =  $tax::all();
        $courseda = $course::find($id);
        $cbunchdetais = coursebunchlist::where('courseid',$id)->get();
        $cspecialization = coursespecializationlist::where('coursessid',$id)->get();
        $branchss = Branch::all();
        $lereports = lecturereport::all();

        $selectedbranch = explode(',', $courseda->branches);
        $ucats = UnviersitiesCategory::all();

        $univfeeslist = universititiesfeeslist::where('coursid',$id)->get();

        return view('superadmin.course.edit',compact('cat','subcat','courseda','taxs','cspecialization','cbunchdetais','branchss','selectedbranch','lereports','ucats','univfeeslist'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\course  $course
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, course $course)
    {
                                $fsd  = $request->fds;
                                $dele = coursebunchlist::where('courseid',$fsd)->get();
                                //dd($dele);
                                $dele->each->delete();


                                $dfe = $request->datske;
                                $deles = coursespecializationlist::where('coursessid',$dfe)->get();
                                $deles->each->delete();


                                $alunivlistdel = $request->alldele;
                                $deleted = universititiesfeeslist::where('coursid',$alunivlistdel)->get();
                                $deleted->each->delete();
                                //dd($alunivlistdel);

                                 $subcourse = $request->subcourse;
                                 $specialia = $request->specializations;

                                 $specilailecut = $request->lessturereports;
                                 $subclectreporst = $request->buncgaelecturerpoerst;
                                 $universiname = $request->univername;
                                 $universifees = $request->univfees;
                                 $bitdfrsss = $request->bitfees;
                                 $trotdfeels = $request->totalfees;
                                 $civsibles = $request->visible;
                                 $byunitsd = $request->universities;


                                 $adfor =  $request->admissionfor;
                                 $anewuni =  $request->newunivFeees;
                                 $abitfe =  $request->bitfessess;
                                 $aoveral =  $request->overallfees;
                              
                                
           






                        if ($request->hasFile('image')) 
                        {

                            $updat = $course::find($id);
                            // dd("test");
                            $image = $request->file('image');
                            $imageName = $image->getClientOriginalName();
                            $name = time().'.'.$image->getClientOriginalExtension();
                            $destinationPath = public_path('/brocheure');
                            //dd($destinationPath);
                            $image->move($destinationPath, $imageName);
                            








                            $branchses = $request->branchses;
                            if(is_array($branchses)) 
                             {
                                
                              
                                 $brans  = implode(',',$branchses);

                              }

                              else
                              {
                                $brans = $request->branchses;

                              }
                            



                                    $updat->cat_id = $request->ccategory;
                                    $updat->subcat_id = $request->csubcategory;
                                    $updat->coursename = $request->coursename;
                                    $updat->bygroup = $request->bygroup;
                                    $updat->byspecialization = $request->byspecification;
                                    $updat->courseprice = $request->courseprice;
                                    $updat->courseonlineprice = $request->onlinecourseprice;
                                    $updat->coursetax = $request->taxe;
                                    $updat->website = $request->websites;
                                    $updat->leadslimitations = $request->leadduration;
                                    $updat->coursedurations = $request->courseduration;
                                    $updat->brocheurefiles = $imageName;
                                    $updat->branches = $brans;
                                    $updat->byuniversitites = $byunitsd;
                                    $updat->bitfees = $bitdfrsss;
                                    $updat->totalfees = $trotdfeels;
                                    $updat->coursevisible = $civsibles;
                                    $updat->mimgurl = $request->miurl;
                                    $updat->markvidurl = $request->mvurl;
                                    $updat->smateriaurl = $request->smurl;
                                    $updat->save();

                                  
                                    

                                    $dsd = $request->fd;
                                    $spdel = $request->fsd;




                                if($subcourse != 0)
                                {
                                    for($i=0; $i < (count($subcourse)); $i++)
                                        {
                                            
                                             $dakmsm = coursebunchlist::updateOrCreate(['bunchcourselists' => $subcourse[$i],'courseid' => $id,'bunlecturereports' => $subclectreporst[$i] ]);

                                          
                  


                                        }
                                }
                                if($adfor != 0)
                                {
                                    for($k=0; $k < (count($adfor)); $k++)

                                    {
                                        $newdataforuniversitiesfesslist = universititiesfeeslist::updateOrCreate(['coursid' => $id,'universitiesfor' => $adfor[$k], 'univfees' => $anewuni[$k] ,'bifees' => $abitfe[$k],'overallfees' => $aoveral[$k]]);
                                    }
                                }

                                if ($specialia != 0) 
                                {
                                    for($j=0; $j < (count($specialia)); $j++)
                                    {
                                    
                                     $productss = coursespecializationlist::updateOrCreate(['cspecializationslists' => $specialia[$j],'coursessid' => $id, 'cspecialilecturereportlis' => $specilailecut[$j] ]);
                                    }  
                                }

                            return redirect('/course')->with('success','Course Updated successfully!');
                        }
                    else
                    {

                            $branchses = $request->branchses;
                            if(is_array($branchses)) 
                             {
                                
                              
                                 $brans  = implode(',',$branchses);

                              }

                              else
                              {
                                $brans = $request->branchses;

                              }
                           
                                    $updat = $course::find($id);
                                    $updat->cat_id = $request->ccategory;
                                    $updat->subcat_id = $request->csubcategory;
                                    $updat->coursename = $request->coursename;
                                    $updat->bygroup = $request->bygroup;
                                    $updat->byspecialization = $request->byspecification;
                                    $updat->courseprice = $request->courseprice;
                                    $updat->courseonlineprice = $request->onlinecourseprice;
                                    $updat->coursetax = $request->taxe;
                                    $updat->website = $request->websites;
                                    $updat->leadslimitations = $request->leadduration;
                                    $updat->coursedurations = $request->courseduration;
                                    $updat->branches = $brans;
                                    $updat->byuniversitites = $byunitsd;
                                    $updat->bitfees = $bitdfrsss;
                                    $updat->totalfees = $trotdfeels;
                                    $updat->coursevisible = $civsibles;
                                    $updat->mimgurl = $request->miurl;
                                    $updat->markvidurl = $request->mvurl;
                                    $updat->smateriaurl = $request->smurl;
                                    $updat->save();

                                    
                                    $dsd = $request->fd;
                                    $spdel = $request->fsd;

                                    
                                if($subcourse != 0)
                                {
                                    for($i=0; $i < (count($subcourse)); $i++)
                                        {
                                            
                                             $dakmsm = coursebunchlist::updateOrCreate(['bunchcourselists' => $subcourse[$i],'courseid' => $id,'bunlecturereports' => $subclectreporst[$i] ]);


                                        }
                                }
                                

                                if ($specialia != 0) 
                                {
                                    for($j=0; $j < (count($specialia)); $j++)
                                    {
                                    
                                    $productss = coursespecializationlist::updateOrCreate(['cspecializationslists' => $specialia[$j],'coursessid' => $id, 'cspecialilecturereportlis' => $specilailecut[$j] ]);
                                    }  
                                }


                                if($adfor != 0)
                                {
                                    for($k=0; $k < (count($adfor)); $k++)

                                    {
                                        $newdataforuniversitiesfesslist = universititiesfeeslist::updateOrCreate(['coursid' => $id,'universitiesfor' => $adfor[$k], 'univfees' => $anewuni[$k] ,'bifees' => $abitfe[$k],'overallfees' => $aoveral[$k]]);
                                    }
                                }
                                 

                                
                        
                   
                                 return redirect('/course')->with('success','Course Updated successfully!');
                     
                    }

      

        

       

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,course $course)
    {
        $dele = $course::find($id);
        $dele->delete();
        return redirect('/course')->with('success','Course Deleted successfully!');
    }

    public function autoompletes($courselistfes,Request $request)
    {
            $data = course::select("coursename")
                ->where("coursename","LIKE","%{$courselistfes}%")
                ->get();
   
        return response()->json($data);


      
        
    }
    
    public function updatebrbacnhces(Request $request)
    {
        $gcourseid = $request->coursesids;
        $cids = implode(',',$gcourseid);

        $branchvalues = Branch::all();

        return view('superadmin.course.upbranches',compact('branchvalues','cids'));

        //dd($gcourseid);
    }

    public function coursewusebranchs(Request $request)
    {
        $coursesid = explode(",",$request->coursid);
      //  dd($coursesid);
        $branch = implode(',',$request->brnahgcscour); 
        $updattes = course::whereIn('id', $coursesid)->update(['branches' => $branch]);

        return redirect('/course')->with('success','Branch Add successfully!');
    }  

    
}
