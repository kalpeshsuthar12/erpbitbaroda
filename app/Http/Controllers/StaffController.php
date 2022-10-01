<?php

namespace App\Http\Controllers;

use App\staff;
use App\User;
use App\Branch;
use App\usercategory;
use App\UsersOfficialsTimingsDetails;
use App\staffpermission;
use App\Designation_Category;
use App\course;
use App\days;
use App\ExpenseCategory;
use App\UsersSalaryaact;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PDF;
use Auth;
use DB;
use Illuminate\Http\Request;


class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function categorywiseajax($uscar)
    {
        $getid =  usercategory::where('usercategoriesname',$uscar)->first();

        $getdesingantions = Designation_Category::where('usecategoid',$getid->id)->get();

        return response()->json($getdesingantions);

    }

    public function index(User $user)
    {
        //
        $staffdata = $user::get();
        
       

        return view('superadmin.staff.manage',compact('staffdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(usercategory $usercategory)
    {
        $uc = usercategory::where('usercategoriesname','!=','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->get();
        $branchse = Branch::get();
        $cors = course::get();
        $ucates = usercategory::where('usercategoriesname','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->get();
        return view('superadmin.staff.create',compact('uc','branchse','cors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,User $user,staffpermission $staffpermission)
    {
       $ucas = $request->ucategory;
       /*$faculcourse = implode(',',$request->fcourse);*/
       $faculcourse = $request->fcourse;

                if(is_array($faculcourse)) 
                 {
                    
                  
                     $fcurses  = implode(',',$faculcourse);

                  }

                  else
                  {
                    $fcurses = $request->fcourse;

                  }

       if($ucas == 'Instructor')
       {
            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->personalemail = $request->emailsss;
            $user->mobileno = $request->mobno;
            $user->usercategory = $ucas;
            $user->facultycourse = $fcurses;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->branchs = $request->brans;
            $user->baddress = $request->baddress;
            $user->bglink = $request->bglink;
            $user->save();

                return redirect('/staff')->with('success','Faculty Register Successfully!!');
       }

        else
        {

            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->personalemail = $request->emailsss;
            $user->mobileno = $request->mobno;
            $user->baddress = $request->usaddress;
            $user->usercategory = $request->ucategory;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->branchs = $request->mbranhcs;
            $user->bglink = $request->bglink;
            $user->save();

            $userid = $user->id;


           
            return redirect('/staff')->with('success','User Created Successfully!!');
        }      
       
    }


    public function storeemployeesdetails(Request $request,User $user)
    {
        $ucas = $request->ucategory;
        $faculcourse = $request->fcourse;

                if(is_array($faculcourse)) 
                 {
                    
                  
                     $fcurses  = implode(',',$faculcourse);

                  }

                  else
                  {
                    $fcurses = $request->fcourse;

                  }

                  $data =  $request->expcateogrys;
                   if(is_array($data)) 
                             {
                            
                                 $excategories  = implode(',',$data);

                              }

                              else
                              {
                                $excategories = $request->leadcourse;

                              }



                


          $empnos = explode("-",$request->enos);

      if($ucas == 'Instructor')
       {

        //dd($request->all());
            $user->cmpnames = $request->compnaynames;
            $user->employeenos = $request->enos;
            $user->enos = $empnos[1];
            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->personalemail = $request->emailsss;
            $user->mobileno = $request->mobno;
            $user->usercategory = $ucas;
            $user->facultycourse = $fcurses;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->ujoiningdate = $request->jdate;
            $user->uaddress = $request->usaddress;
            $user->udeposite = $request->deposite;
            $user->udestartsdates = $request->dstartdate;
            $user->udeendsdates = $request->denddates;
            $user->apleaves = $request->mapcheckbos;
            $user->paidsleaves = $request->mpaidsleaves;
            $user->expcategories = $excategories;
             $user->uremarks =  $request->Remarks;
             $user->udesignations =  $request->designations;
            $user->save();

            $userid = $user->id;
            $branchsdetails = $request->brnachs;
            $modesdet = $request->teachmodes;
            $das = $request->days;
            $intms = $request->intimings;
            $outtms = $request->outtimings;
            $ussalarys = $request->salarys;
            //$ussalarys = $request->salarys;


                        for($i=0; $i < (count($branchsdetails)); $i++)
                        {
                                    $UsersOfficialsTimingsDetails = new UsersOfficialsTimingsDetails([
                                    
                                    'usersdetailsid' => $userid,
                                    'usersdetailsbranchs'   => $branchsdetails[$i],
                                    'usersdetailsmodes'   => $modesdet[$i],
                                    'usersdetailsdays'   => $das[$i],
                                    'usersdetailsintimings'   => $intms[$i],
                                    'usersdetailsouttimings'   => $outtms[$i],
                                    'salarys'   => $ussalarys[$i],
                                    
                                ]);
                                $UsersOfficialsTimingsDetails->save();
                        }


                        $data["UserNames"] = $request->fullname;;
        $data["Userpasswords"] = $request->pass;
        $data["Useremail"] = $request->emails;

        
            Mail::send('superadmin.staff.sentusersmails', $data, function ($message) use ($data) {
            $data;
            $message->to($data["Useremail"],$data["UserNames"])
                ->from('info@bitbaroda.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Welcome letter to New Instructor.");
                
        });


                return redirect('/instructors-details')->with('success','Faculty Register Successfully!!');
       }

        else
        {

             $user->cmpnames = $request->compnaynames;
            $user->employeenos = $request->enos;
            $user->enos = $empnos[1];
            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->mobileno = $request->mobno;
            $user->branchs = $request->mbranhcs;
            $user->mdaysu = $request->mday;
            $user->mintimings = $request->mintiminsgs;
            $user->mouttimings = $request->moutstiminsgs;
            $user->usercategory = $ucas;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->ujoiningdate = $request->jdate;
            $user->uaddress = $request->usaddress;
            $user->usalarys = $request->msalarys;
            $user->udeposite = $request->deposite;
            $user->udestartsdates = $request->dstartdate;
            $user->udeendsdates = $request->denddates;
            $user->apleaves = $request->mapcheckbos;
            $user->paidsleaves = $request->mpaidsleaves;
            $user->expcategories =  $excategories;
            $user->expcategories =  $excategories;
             $user->udesignations =  $request->designations;
            $user->uremarks =  $request->Remarks;
            $user->save();

                     $data["UserNames"] = $request->fullname;;
                $data["Userpasswords"] = $request->pass;
                $data["Useremail"] = $request->emails;

             Mail::send('superadmin.staff.sentusersmails', $data, function ($message) use ($data) {
            $data;
            $message->to($data["Useremail"],$data["UserNames"])
                ->from('info@bitbaroda.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Welcome letter to New Instructor.");
                
        });
            

            $userid = $user->id;
            $ussalarys = $request->ussalarys;
            $usremarks = $request->usremarks;
           

                      for($i=0; $i < (count($ussalarys)); $i++)
                        {
                                    $UsersSalaryaact = new UsersSalaryaact([
                                    
                                    'acivusersid' => $userid,
                                    'achivsalarys'   => $ussalarys[$i],
                                    'achivremarks'   => $usremarks[$i],
                                   
                                    
                                ]);
                                $UsersSalaryaact->save();
                        }



           
            return redirect('/others-users-details')->with('success','User Created Successfully!!');
        }  
    }
  

   public function updateinstructors($id,Request $request)
    {

            $dele = UsersOfficialsTimingsDetails::where('usersdetailsid',$id)->get();
            $dele->each->delete();


            $user = User::find($id);

        $ucas = $request->ucategory;
        $faculcourse = $request->fcourse;
        $cdatas = $request->checkboxsdata;

                if(is_array($faculcourse)) 
                 {
                    
                  
                     $fcurses  = implode(',',$faculcourse);

                  }

                  else
                  {
                    $fcurses = $request->fcourse;

                  }
                  $data =  $request->expcateogrys;
                   if(is_array($data)) 
                             {
                            
                                 $excategories  = implode(',',$data);

                              }

                              else
                              {
                                $excategories = $request->leadcourse;

                              }


                              if(is_array($cdatas)) 
                             {
                            
                                 $checkdatas  = implode(',',$cdatas);

                              }

                              else
                              {
                                $checkdatas = $request->checkboxsdata;

                              }


                


          $empnos = explode("-",$request->enos);

            $user->cmpnames = $request->compnaynames;
            $user->employeenos = $request->enos;
            $user->enos = $empnos[1];
            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->personalemail = $request->emailsss;
            $user->mobileno = $request->mobno;
            $user->usercategory = $ucas;
            $user->facultycourse = $fcurses;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->ujoiningdate = $request->jdate;
            $user->uaddress = $request->usaddress;
           $user->udeposite = $request->deposite;
            $user->udestartsdates = $request->dstartdate;
            $user->udeendsdates = $request->denddates;
            $user->apleaves = $request->mapcheckbos;
            $user->paidsleaves = $request->mpaidsleaves;
            $user->expcategories =  $excategories;
            $user->acccheckbox = $checkdatas;
            $user->accbanksremarks = $request->bremarks;
             $user->uremarks =  $request->Remarks;
            $user->save();

            $userid = $user->id;
            $branchsdetails = $request->brnachs;
            $modesdet = $request->teachmodes;
            $das = $request->days;
            $intms = $request->intimings;
            $outtms = $request->outtimings;
            $ussalarys = $request->salarys;


                        for($i=0; $i < (count($branchsdetails)); $i++)
                        {
                                    $UsersOfficialsTimingsDetails = new UsersOfficialsTimingsDetails([
                                    
                                    'usersdetailsid' => $userid,
                                    'usersdetailsbranchs'   => $branchsdetails[$i],
                                    'usersdetailsmodes'   => $modesdet[$i],
                                    'usersdetailsdays'   => $das[$i],
                                    'usersdetailsintimings'   => $intms[$i],
                                    'usersdetailsouttimings'   => $outtms[$i],
                                    'salarys'   => $ussalarys[$i],
                                    
                                ]);
                                $UsersOfficialsTimingsDetails->save();
                        }


                        $data["UserNames"] = $request->fullname;;
        $data["Userpasswords"] = $request->pass;
        $data["Useremail"] = $request->emails;

        
           /* Mail::send('superadmin.staff.sentusersmails', $data, function ($message) use ($data) {
            $data;
            $message->to($data["Useremail"],$data["UserNames"])
                ->from('mshah0140@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Welcome letter to New Instructor.");
                
        });*/


                return redirect('/instructors-details')->with('success','Faculty Updated Successfully!!');
     
    }


    public function updateothersusers($id,Request $request)
    {
              $user = User::find($id);
               //$userscLE = UsersSalaryaact::where('acivusersid',$id)->get();
              $cdatas = $request->checkboxsdata;

                $dele = UsersSalaryaact::where('acivusersid',$id)->get();
                $dele->each->delete();


              $empnos = explode("-",$request->enos);
              $ucas = $request->ucategory;

              $data =  $request->expcateogrys;
                   if(is_array($data)) 
                             {
                            
                                 $excategories  = implode(',',$data);

                              }

                              else
                              {
                                $excategories = $request->leadcourse;

                              }


                              if(is_array($cdatas)) 
                             {
                            
                                 $checkdatas  = implode(',',$cdatas);

                              }

                              else
                              {
                                $checkdatas = $request->checkboxsdata;

                              }

          $user->cmpnames = $request->compnaynames;
            $user->employeenos = $request->enos;
            $user->enos = $empnos[1];
            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->mobileno = $request->mobno;
            $user->branchs = $request->mbranhcs;
            $user->mdaysu = $request->mday;
            $user->mintimings = $request->mintiminsgs;
            $user->mouttimings = $request->moutstiminsgs;
            $user->usercategory = $ucas;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->ujoiningdate = $request->jdate;
            $user->uaddress = $request->usaddress;
            $user->usalarys = $request->msalarys;
            $user->udeposite = $request->deposite;
            $user->udestartsdates = $request->dstartdate;
            $user->udeendsdates = $request->denddates;
             $user->apleaves = $request->mapcheckbos;
            $user->paidsleaves = $request->mpaidsleaves;
            $user->expcategories = $excategories;
            $user->acccheckbox = $checkdatas;
            $user->accbanksremarks = $request->bremarks;
             $user->uremarks =  $request->Remarks;
            $user->save();


                 $userid = $user->id;
            $ussalarys = $request->ussalarys;
            $usremarks = $request->usremarks;
           

                      for($i=0; $i < (count($ussalarys)); $i++)
                        {
                                    $UsersSalaryaact = new UsersSalaryaact([
                                    
                                    'acivusersid' => $userid,
                                    'achivsalarys'   => $ussalarys[$i],
                                    'achivremarks'   => $usremarks[$i],
                                   
                                    
                                ]);
                                $UsersSalaryaact->save();
                        }


            


           
            return redirect('/others-users-details')->with('success','User Updated Successfully!!');
     
    }


    public function othersusersdetails()
    {
         $usersdetails = User::where('usercategory','!=','Instructor')->where('usercategory','!=','Student')->where('usercategory','!=','Franchise')->where('usercategory','!=','Affiliate Marketing')->get();
         return view('superadmin.staff.othersuersdetails',compact('usersdetails'));
    }

    public function instructorsdetails()
    {
         $cors = course::get();
         $usersdetails = User::where('usercategory','Instructor')->get();
         return view('superadmin.staff.instructorsdetails',compact('cors','usersdetails'));
    }
    public function filtersinstructorsdetails(Request $request)
    {
        $coursede = $request->courses;
        $cors = course::get();
        $usersdetails = User::where('usercategory','Instructor')->whereRaw('FIND_IN_SET("'.$coursede.'",facultycourse)')->get();

         return view('superadmin.staff.filterinstructorsdetails',compact('cors','usersdetails','coursede'));
    }


    public function storestudentssetails(Request $request,User $user)
    {

            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->personalemail = $request->emailsss;
            $user->mobileno = $request->mobno;
            $user->usercategory = $request->ucategory;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->save();

            return redirect('/students-details')->with('success','Students Register Successfully!!');
    }



     public function updatestudetnsdetails(Request $request,$id)
    {
              $user = User::find($id);

            $user->name = $request->fullname;
            $user->email = $request->emails;
            $user->personalemail = $request->emailsss;
            $user->mobileno = $request->mobno;
            $user->usercategory = $request->ucategory;
            $user->password = Hash::make($request->pass);
            $user->sepass = $request->pass;
            $user->save();

            return redirect('/students-details')->with('success','Students Updated Successfully!!');
    }

public function studentssetails(Request $request,User $user)
    {

            $usersdetails = User::where('usercategory','Student')->get();

            return view('superadmin.staff.studentsdetails',compact('usersdetails'));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show(staff $staff)
    {
        
        $uc = usercategory::where('usercategoriesname','!=','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->where('usercategoriesname','!=','Student')->get();
        $branchse = Branch::get();
        $dyas = days::get();
        $cors = course::get();
      //  $ucates = usercategory::where('usercategoriesname','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->get();
         $latests = User::latest()->get()->pluck('enos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITIN/'.$code_nos;
            //return response()->json($value);
            $expenc = ExpenseCategory::all();
 
        return view('superadmin.staff.createemployee',compact('uc','branchse','cors','value','dyas','expenc'));
    }

    public function editinstructors($id)
    {

        $usersdata = User::find($id);
        $uc = usercategory::where('usercategoriesname','!=','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->where('usercategoriesname','!=','Student')->get();
        $branchse = Branch::get();
        $dyas = days::get();
        $cors = course::get();
        $selectedcourse = explode(',', $usersdata->facultycourse);
      //  $ucates = usercategory::where('usercategoriesname','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->get();
         $latests = User::latest()->get()->pluck('enos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITIN/'.$code_nos;

            $facultyswithusers = UsersOfficialsTimingsDetails::where('usersdetailsid',$id)->get();
            $expenc = ExpenseCategory::all();
            $selectedexpcategoruws = explode(',', $usersdata->expcategories);
            //$expenc = ExpenseCategory::all();

            return view('superadmin.staff.editinstructors',compact('uc','branchse','cors','value','dyas','usersdata','selectedcourse','facultyswithusers','expenc','selectedexpcategoruws'));
    }

      public function editotherusers($id)
    {

        $usersdata = User::find($id);
        $uc = usercategory::where('usercategoriesname','!=','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->where('usercategoriesname','!=','Student')->get();
        $branchse = Branch::get();
        $dyas = days::get();
        $cors = course::get();
       // $selectedcourse = explode(',', $usersdata->facultycourse);
      //  $ucates = usercategory::where('usercategoriesname','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->get();
         $latests = User::latest()->get()->pluck('enos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITIN/'.$code_nos;

            //$facultyswithusers = UsersOfficialsTimingsDetails::where('usersdetailsid',$id)->get();
            $selectedexpcategoruws = explode(',', $usersdata->expcategories);
            $expenc = ExpenseCategory::all();
            $userscLE = UsersSalaryaact::where('acivusersid',$id)->get();
            //dd($selectedexpcategoruws);
            return view('superadmin.staff.editotherusers',compact('uc','branchse','cors','value','dyas','usersdata','selectedexpcategoruws','expenc','userscLE'));
    }


     public function editstudennts($id)
    {

        $usersdata = User::find($id);
        $uc = usercategory::where('usercategoriesname','Student')->get();
       

            return view('superadmin.staff.editstudetnts',compact('uc','usersdata'));
    }

    public function studetns(staff $staff)
    {
        
        $uc = usercategory::where('usercategoriesname','=','Student')->get();
        $branchse = Branch::get();
        $dyas = days::get();
        $cors = course::get();
      //  $ucates = usercategory::where('usercategoriesname','Affiliate Marketing')->where('usercategoriesname','!=','Franchise')->get();
         $latests = User::latest()->get()->pluck('enos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITIN/'.$code_nos;
            //return response()->json($value);

        return view('superadmin.staff.createstudents',compact('uc','branchse','cors','value','dyas'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit($id,staff $staff,User $user,staffpermission $staffpermission)
    {
          $deleteuser = User::find($id);
        //$deleteuser = staffpermission::where('user_id', '=',$id);
        $deleteuser->delete();

        return redirect('/students-details')->with('success','Student Deleted Successfully!!');
        
    }


    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function update($id,User $user,Request $request, staff $staff)
    {
        $deleteuser = User::find($id);
        //$deleteuser = staffpermission::where('user_id', '=',$id);
        $deleteuser->delete();

        return redirect('/others-users-details')->with('success','User Deleted Successfully!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,staff $staff,User $user,staffpermission $staffpermission)
    {
        //dd($deleteuser);
        $deleteuser = User::find($id);
        //$deleteuser = staffpermission::where('user_id', '=',$id);
        $deleteuser->delete();

        $dele = UsersOfficialsTimingsDetails::where('usersdetailsid', '=',$id);
        $dele->delete();


       return redirect('/instructors-details')->with('success','Instructors Deleted Successfully!!');


    }

    public function activate($id,staff $staff,User $user,staffpermission $staffpermission)
    {
        //dd($deleteuser);
         //dd($deleteuser);
        $deleteuser = User::find($id);
        $deleteuser->userstatus = 1; 
        $deleteuser->save();


        return redirect()->back()->with('success','User Activated Successfully!!');


    }

    public function deactivates($id,staff $staff,User $user,staffpermission $staffpermission)
    {
        //dd($deleteuser);
        $deleteuser = User::find($id);
        $deleteuser->userstatus = 0; 
        $deleteuser->save();

    
       return redirect()->back()->with('success','User Deactivated Successfully!!');


    }
}
