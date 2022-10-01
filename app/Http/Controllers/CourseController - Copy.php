<?php
namespace App\Http\Controllers;
use App\Branch;
use App\course;
use App\coursecategory;
use App\coursesubcategory;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\lecturereport;
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
                ->join('taxes', 'taxes.id', '=', 'courses.coursetax')
                ->select('courses.id','coursesubcategories.subcategory_name', 'coursecategories.coursecategoryname','courses.created_at','taxes.taxrate','courses.coursename','courses.courseprice','courses.courseonlineprice','courses.website','courses.leadslimitations','courses.coursedurations','courses.bygroup')
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
        //$coursesubcat = $coursecategory::get();

        return view('superadmin.course.create',compact('taxes','coursecat'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,course $course)
    {

        //dd($request->all());
         if ($request->hasFile('image')) 
         {
                // dd("test");
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/brocheure');
                $image->move($destinationPath, $imageName);



                        $coursemodel = new course();
                        $courses = $coursemodel->create([
                            'cat_id'=> $request->ccategory,
                            'subcat_id'=> $request->csubcategory,
                            'coursename'=> $request->coursename,
                            'bygroup'=> $request->bygroup,
                            'courseprice'=> $request->courseprice,
                            'courseonlineprice'=> $request->onlinecourseprice,
                            'coursetax'=> $request->taxe,
                            'leadslimitations'=> $request->leadduration,
                            'coursedurations'=> $request->courseduration,
                            'brocheurefiles'=> $imageName,
                            'website'=> $request->websites,
                           
                        ]);

                return redirect('/course')->with('success','Course created successfully!');
        }
        else
        {
             $coursemodel = new course();
        $courses = $coursemodel->create([
            'cat_id'=> $request->ccategory,
            'subcat_id'=> $request->csubcategory,
            'coursename'=> $request->coursename,
            'bygroup'=> $request->bygroup,
            'courseprice'=> $request->courseprice,
            'courseonlineprice'=> $request->onlinecourseprice,
            'coursetax'=> $request->taxe,
            'website'=> $request->websites,
            'leadslimitations'=> $request->leadduration,
            'coursedurations'=> $request->courseduration,
          
            
        ]);
        return redirect('/course')->with('success','Course created successfully!');
         
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

        $selectedbranch = explode(',', $courseda->branches);
        $lereports = lecturereport::all();

        return view('superadmin.course.edit',compact('cat','subcat','courseda','taxs','cspecialization','cbunchdetais','branchss','selectedbranch','lereports'));
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
            


         if ($request->hasFile('image')) 
         {

            $updat = $course::find($id);
                // dd("test");
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/brocheure');
                $image->move($destinationPath, $imageName);
                $universiname = $request->univername;
                $universifees = $request->univfees;
                $bitdfrsss = $request->bifee;
                $trotdfeels = $request->totalunifees;

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
                        $updat->universitnames = $universiname;
                        $updat->universitiesfees = $universifees;
                        $updat->bitfees = $bitdfrsss;
                        $updat->totalfees = $trotdfeels;
                        $updat->save();

                        $subcourse = $request->subcourse;
                        $specialia = $request->specializations;
                        

                        $dsd = $request->fd;
                        $spdel = $request->fsd;




                    for($i=0; $i < (count($subcourse)); $i++)
                    {
                        
                         $dakmsm = coursebunchlist::updateOrCreate(['bunchcourselists' => $subcourse[$i],'courseid' => $id ]);
                    } 

                    for($j=0; $j < (count($specialia)); $j++)
                    {
                        
                         $productss = coursespecializationlist::updateOrCreate(['cspecializationslists' => $specialia[$j],'coursessid' => $id ]);
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
                        $updat->universitnames = $request->univername;;
                        $updat->universitiesfees =$request->univfees;
                        $updat->bitfees = $request->bitfees;
                        $updat->totalfees = $request->totalfees;
                        $updat->save();

                         $subcourse = $request->subcourse;
                        $specialia = $request->specializations;
                        $dsd = $request->fd;
                        $spdel = $request->fsd;

                        



                    for($i=0; $i < (count($subcourse)); $i++)
                    {
                        
                         $sams = coursebunchlist::updateOrCreate(['bunchcourselists' => $subcourse[$i],'courseid' => $id ]);
                    } 

                    for($j=0; $j < (count($specialia)); $j++)
                    {
                        
                         $productss = coursespecializationlist::updateOrCreate(['cspecializationslists' => $specialia[$j],'coursessid' => $id ]);
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
}
