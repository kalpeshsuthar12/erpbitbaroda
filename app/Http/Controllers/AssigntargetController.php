<?php

namespace App\Http\Controllers;

use App\assigntarget;
use App\Branch;
use App\usercategory;
use App\User;
use App\logsData;
use App\TargetAlloted;
use App\payment;
use App\admissionprocess; 
use App\AffiliatesCategory; 
use Illuminate\Http\Request;
use DB;
use Auth;

class AssigntargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $targetsdata = assigntarget::orderBy('id','DESC')->get();
      //   dd($targetsdata);

                return view('superadmin.assigntarget.manage',compact('targetsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(usercategory $usercategory)
    {
        $ucateg = usercategory::get();
        $branchse = Branch::get();
        $branchse = Branch::get();
        $acats = AffiliatesCategory::get();
        return view('superadmin.assigntarget.create',compact('ucateg','branchse','acats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,assigntarget $assigntarget)
    {
        $user = Auth::user();
       /* dd($request->all());*/
              
               $basetargets = $request->baseamount;
               $targetsdat = $request->targetamou;
               $ttargets = $request->totaltargets;
               $tincentinvre = $request->incentives;

               $btttarget = $request->btotaltargets;
               $bincentive = $request->bincentives;


                if($data =  $request->assignedtargetto)
                {

                  foreach($data as $datas)
                   {
                         $assigntargetmodel = new assigntarget();
                            $assigntarget = $assigntargetmodel->create([
                                'targtname'=> $request->tname,
                                'tmonth'=> $request->months,
                                'bycb'=> $request->bycb,
                                'usercategory'=> $request->usercategory,
                                'tassignuser'=> $datas,
                                'tbranch'=> $request->brnahc,
                                'targetamount'=> $request->tamount,
                                'incentivepercent'=> $request->tper,
                                'startsdates'=> $request->startdate,
                                'enddates'=> $request->enddate,
                                'affiliatescateogry'=> $request->affiliatescategname,
                            ]);

                            $getid = $assigntarget->id;

                            for($i=0; $i < (count($targetsdat)); $i++)
                        {
                                $targetAlloted = new TargetAlloted([
                                
                                'targetuserid' => $getid,
                                'basetarget' => $basetargets[$i],
                                'targetamounts'   => $targetsdat[$i],
                                'totaltargets'   => $ttargets[$i],
                                'incentive'   => $tincentinvre[$i],
                                
                            ]);
                            $targetAlloted->save();
                        }
                   }

                }
                else
                {   $targetsbybranch = $request->brnahc;
                    if($targetsbybranch)
                    {
                        $assigntargetmodel = new assigntarget();
                            $assigntarget = $assigntargetmodel->create([
                                'targtname'=> $request->tname,
                                'tmonth'=> $request->months,
                                'bycb'=> $request->bycb,
                                'usercategory'=> $request->usercategory,
                                'tassignuser'=> $request->assignedtargetto,
                                'tbranch'=> $request->brnahc,
                                'targetamount'=> $request->tamount,
                                'incentivepercent'=> $request->tper,
                                'startsdates'=> $request->startdate,
                                'enddates'=> $request->enddate,
                                'affiliatescateogry'=> $request->affiliatescategname,
                            ]);

                            $getid = $assigntarget->id;

                            for($i=0; $i < (count($btttarget)); $i++)
                        {
                                $targetAlloted = new TargetAlloted([
                                
                                'targetuserid' => $getid,
                                'totaltargets'   => $btttarget[$i],
                                'incentive'   => $bincentive[$i],
                                
                            ]);
                            $targetAlloted->save();
                        }

                    }
                     
                }
        
                           

                

        /*$assigntarget['user_id'] = $user->id;*/

        if ($assigntarget) {
            $logsDatamodel = new logsData();
        $logsData = $logsDatamodel->create([
            'logsdescription'=> $assigntarget->targtname.' Created By '.$user->name,
            
        ]);

          
            return redirect('/target')->with('success','Target Created Successfully');
        } 
        else {
           
             return redirect('/create-target')->with('success','Oops something went wrong, Target not saved');
        }

         //return redirect('/target')->with('success','Target Created Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\assigntarget  $assigntarget
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $currentMonth = date('m');

        $branchse = Branch::get();

        $marskeUse =  User::where('usercategory','Marketing')->get();

        $userId = Auth::user()->id;

        $userBranch = Auth::user()->branchs;

        $userName = Auth::user()->name;
        
        $admissionId = admissionprocess::select('payments.paymentreceived')->join("payments","admissionprocesses.id","=","payments.inviceid")->join("users","users.id","=","admissionprocesses.admissionsusersid")->sum('payments.paymentreceived');
        /*$admissionId = 1000000;*/
        
        $Tars = assigntarget::select('assigntargets.*','target_alloteds.*')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('target_alloteds.statsus',1)->whereMonth('assigntargets.enddates',$currentMonth)->get();

         return view('superadmin.assigntarget.incentcalcu',compact('admissionId','Tars','branchse','marskeUse'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\assigntarget  $assigntarget
     * @return \Illuminate\Http\Response
     */
    public function edit($id,assigntarget $assigntarget,User $user)
    {
        $ucateg = usercategory::get();
        $editds = assigntarget::find($id);
        $branchse = Branch::get();
        $selectedusers = explode(',', $editds->tassignuser);
        $userdata = User::get();

        $tallotes = TargetAlloted::where('targetuserid',$id)->get();


        return view('superadmin.assigntarget.edit',compact('editds','ucateg','userdata','selectedusers','branchse','tallotes'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\assigntarget  $assigntarget
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, assigntarget $assigntarget)
    {
        
        //dd($request->all);
         $data =  $request->assignedtargetto;
         /*dd($request->all());*/

      
       if(is_array($data)) 
                 {
                    
                  
                     $assignusers  = implode(',',$data);

                  }

                  else
                  {
                    $assignusers = $request->assignedtargetto;

                  }

                                $fds  = $request->fd;
                                $bids  = $request->branchtargetid;
                                
                                $dele = TargetAlloted::where('targetuserid',$fds)->get();
                                $dele->each->delete();

                                
                                $deles = TargetAlloted::where('targetuserid',$bids)->get();
                                $deles->each->delete();

        $tamousn  = $request->targetamou;
        $ince  = $request->incentives;
        $bsetargets  = $request->baseamount;
        $ttotalamou  = $request->totaltargets;
        //$stats  = $request->statsys;
                $bttargets = $request->btotaltargets;
                $btive = $request->bincentives;
           // dd($bttargets);

        $branchwise = $request->brnahc;
        

              if($request->brnahc)
              {
                       
                 
                       
                     

                            for($j=0; $j < (count($bttargets)); $j++)
                                    {
                                    
                                     $productss = TargetAlloted::updateOrCreate(['targetuserid' => $id,'totaltargets' => $bttargets[$j],'incentive' => $btive[$j] ]);
                                    }  


              }

              else
              {
                   
                     for($i=0; $i < (count($tamousn)); $i++)
                            {
                                
                                 $productss = TargetAlloted::updateOrCreate(['targetuserid' => $id,'basetarget'=>$bsetargets[$i],'targetamounts' => $tamousn[$i],'totaltargets' => $ttotalamou[$i],'incentive' => $ince[$i]]);
                            }
              }
             

          




        $updatedsdata = assigntarget::find($id);
        $updatedsdata->targtname = $request->tname;
        $updatedsdata->tmonth = $request->months;
        $updatedsdata->bycb = $request->bycb;
        $updatedsdata->usercategory = $request->usercategory;
        $updatedsdata->tbranch = $request->brnahc;
        $updatedsdata->tassignuser = $assignusers;
        $updatedsdata->targetamount = $request->tamount;
        $updatedsdata->incentivepercent = $request->tper;
        $updatedsdata->startsdates = $request->startdate;
        $updatedsdata->enddates = $request->enddate;
        $updatedsdata->save();


       

        
            


         

        


        return redirect('/target')->with('success','Target Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\assigntarget  $assigntarget
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,assigntarget $assigntarget)
    {
        $user = Auth::user();
        $dels = assigntarget::find($id);
        $dels->delete();
       

        if ($dels) {
            $logsDatamodel = new logsData();
        $logsData = $logsDatamodel->create([
            'logsdescription'=> $dels->targtname.' Deleted By '.$user->name,
            
        ]);

          
            return redirect('/target')->with('success','Target Deleted Successfully');
        } 
        else {
           
             return redirect('/delete-target/'.$updatedsdata->id)->with('success','Oops something went wrong, Target not saved');
        }
    }

    public function ajax($userCategory,User $user)
    {
         $usersdata = DB::table("users")
                    ->select("name","id")
                    ->where("usercategory",$userCategory)
                    ->get();

         return json_encode($usersdata);
    }

    public function targetlists(Request $request)
    {
        $tlists = $request->incentiveslis;

        $data= array();

        $result = TargetAlloted::where('targetuserid',$tlists)->orderBy('id','DESC')->get();

        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->basetarget;
            $row[] = $res->targetamounts;
            $row[] = $res->totaltargets;
            $row[] = $res->incentive."%";
            $row[] = "";
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }


    public function filtersData(Request $request)
    {
        $bdata = $request->branchData;
        $udata = $request->usersData;

            if($bdata)
            {
                $targetmonths = $request->nameMonth;

                $newDats = explode("-", $targetmonths);
               // dd($newDats);
                $branchse = Branch::get();

                $marskeUse =  User::where('usercategory','Marketing')->get();

                $admissionId = admissionprocess::select('payments.paymentreceived')->join("payments","admissionprocesses.id","=","payments.inviceid")->where('admissionprocesses.stobranches',$bdata)->whereYear('payments.paymentdate', $newDats[0])->whereMonth('payments.paymentdate', $newDats[1])->sum('payments.paymentreceived');

                 $Tars = assigntarget::select('assigntargets.*','target_alloteds.*')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tbranch',$bdata)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->get();
        
                $TotalsTargest = assigntarget::select('target_alloteds.totaltargets')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tbranch',$bdata)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->sum('target_alloteds.totaltargets');

                return view('superadmin.assigntarget.filterbycalculationsbybranchs',compact('admissionId','Tars','TotalsTargest','branchse','marskeUse','bdata'));

            }

            else if($udata)
            {

                /*$newDats = $request->nameMonth;*/

                $targetmonths = $request->nameMonth;

                $newDats = explode("-", $targetmonths);

                $branchse = Branch::get();

                $marskeUse =  User::where('usercategory','Marketing')->get();

                $userId  = User::where('name',$udata)->pluck('id');
                $admissionId = admissionprocess::select('payments.paymentreceived')->join("payments","admissionprocesses.id","=","payments.inviceid")->where('admissionprocesses.admissionsusersid',$userId)->whereYear('payments.paymentdate', $newDats[0])->whereMonth('payments.paymentdate', $newDats[1])->sum('payments.paymentreceived');


                $Tars = assigntarget::select('assigntargets.*','target_alloteds.*','users.name')->join("users","users.name","=","assigntargets.tassignuser")->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tassignuser',$udata)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->get();
        
                $TotalsTargest = assigntarget::select('target_alloteds.totaltargets')->join("target_alloteds","assigntargets.id","=","target_alloteds.targetuserid")->where('assigntargets.tassignuser',$udata)->whereYear('assigntargets.enddates', $newDats[0])->whereMonth('assigntargets.enddates', $newDats[1])->sum('target_alloteds.totaltargets');

                         //dd($TotalsTargest);
                     return view('superadmin.assigntarget.filterbycalculations',compact('admissionId','Tars','TotalsTargest','branchse','marskeUse'));
            }

    }
}
