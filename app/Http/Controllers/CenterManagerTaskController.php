<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\task;
use App\Notifications\TaskCompletes;
use Notification;
use Auth;
use DB;

class CenterManagerTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $currentMonth = date('m');
         $userId = Auth::user()->id;
          $userName = Auth::user()->name;
             $taskdata = task::where('tassugnto',$userName)->orWhere('tassignfrom',$userName)->whereMonth('duedate',$currentMonth)->get();
         

          //  dd($taskdata);


            return view('centremanager.task.manage',compact('taskdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $usersdata = User::all();
       return view('centremanager.task.create',compact('usersdata'));
          
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
                        
                        $user = User::where('name',$request->assigneto)->first();
                                 //$af = User::where('name',$request->assigneto)->first();

                                        $user->notify(new TaskCompletes(task::findOrFail($task->id)));

                return redirect('/centremanager-task')->with('success','Tasks created successfully!');
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
                                 $user = User::where('name',$request->assigneto)->first();
                                 //$af = User::where('name',$request->assigneto)->first();

                                        $user->notify(new TaskCompletes(task::findOrFail($task->id)));
        return redirect('/centremanager-task')->with('success','Tasks created successfully!');
         
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
        $getid = task::find($id);
        $usersdata = User::all();

        return view('centremanager.task.edit',compact('getid','usersdata'));
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
         $userId = Auth::user()->id;

         $updatetask = task::find($id);

        
        if ($request->hasFile('image')) 
         {
                //dd($request->all());
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/taskfiles');
                $image->move($destinationPath, $imageName);

                        $updatetask->taskname = $request->taskname;
                        $updatetask->tassugnto  = $request->assigneto;
                        $updatetask->tassignfrom  = $request->assignfrom;
                        $updatetask->startdate = $request->startdate;
                        $updatetask->duedate = $request->duedate;
                        $updatetask->tasksfiles = $imageName;
                        $updatetask->tasdescription = $request->tdescription;
                        $updatetask->status = $request->taskstutsstust;
                        $updatetask->save();

                return redirect('/centremanager-task')->with('success','Tasks created successfully!');
        }
        else
        {       

                        $updatetask->taskname = $request->taskname;
                        $updatetask->tassugnto  = $request->assigneto;
                        $updatetask->tassignfrom  = $request->assignfrom;
                        $updatetask->startdate = $request->startdate;
                        $updatetask->duedate = $request->duedate;
                        $updatetask->tasdescription = $request->tdescription;
                        $updatetask->status =  $request->taskstutsstust;
                        $updatetask->save();


        return redirect('/centremanager-task')->with('success','Tasks created successfully!');
         
        }
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
