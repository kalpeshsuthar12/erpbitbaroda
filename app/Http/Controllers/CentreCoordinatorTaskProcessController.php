<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\task;
use Auth;
use DB;

class CentreCoordinatorTaskProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $userId = Auth::user()->id;
          $userName = Auth::user()->name;
          $taskdata = task::where('tassugnto',$userName)->orWhere('tassignfrom',$userName)->get();
         dd($taskdata);
            return view('centrecoordinator.task.manage',compact('taskdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $usersdata = User::all();
       return view('centrecoordinator.task.create',compact('usersdata'));
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

     //    dd($userId);
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

                return redirect('/centre-coordinator-task')->with('success','Tasks created successfully!');
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
        return redirect('/centre-coordinator-task')->with('success','Tasks created successfully!');
         
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
