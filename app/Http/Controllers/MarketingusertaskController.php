<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\task;
use Auth;
use DB;

class MarketingusertaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
           $currentMonth = date('m');
          $userName= Auth::user()->name;
          $taskdata = task::where('tassugnto',$userName)->whereMonth('duedate',$currentMonth)->get();
         

          //  dd($taskdata);


            return view('marketing.task.manage',compact('taskdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        $usersdata = User::all();
        return view('marketing.task.create',compact('usersdata'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         $userId = Auth::user()->id;
        if ($request->hasFile('image')) 
         {
                //dd($request->all());
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/taskfiles');
                $image->move($destinationPath, $imageName);



                        $taskmodel = new task();
                        $task = $taskmodel->create([
                            'taskname'=> $request->taskname,
                            'tassugnto'=> $request->assigneto,
                            'tassignfrom'=> $request->assignfrom,
                            'startdate'=> $request->startdate,
                            'duedate'=> $request->duedate,
                            'tasksfiles'=> $imageName,
                            'tasdescription'=> $request->tdescription,
                            'userid'=> $userId,
                            
                           
                        ]);

                return redirect('/marketing-users-task')->with('success','Tasks created successfully!');
        }
        else
        {
                            $taskmodel = new task();
                            $task = $taskmodel->create([
                            'taskname'=> $request->taskname,
                            'tassugnto'=> $request->assigneto,
                            'tassignfrom'=> $request->assignfrom,
                            'startdate'=> $request->startdate,
                            'duedate'=> $request->duedate,
                            'tasdescription'=> $request->tdescription,
                            'userid'=> $userId,
          
            
                                  ]);
        return redirect('/marketing-users-task')->with('success','Tasks created successfully!');
         
        }
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
