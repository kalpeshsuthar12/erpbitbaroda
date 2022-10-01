<?php
namespace App\Http\Controllers;
use App\task;
use App\taskfollowups;
use App\User;
use App\Notifications\TaskCompletes;
use DB;
use Notification;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $taskdata = task::get();
 

            return view('superadmin.task.manage',compact('taskdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        $usersdata = User::all();
        return view('superadmin.task.create',compact('usersdata'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,task $task)
    {

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
                                    
                                    
                                   
                                ]);
                                
                                 $user = User::where('name',$request->assigneto)->first();
                                 //$af = User::where('name',$request->assigneto)->first();

                                        $user->notify(new TaskCompletes(task::findOrFail($task->id)));

                        return redirect('/tasks')->with('success','Tasks created successfully!');
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
                                   
                  
                    
                                          ]);
                                          
                             $user = User::where('name',$request->assigneto)->first();
                                 //$af = User::where('name',$request->assigneto)->first();

                                        $user->notify(new TaskCompletes(task::findOrFail($task->id)));
                return redirect('/tasks')->with('success','Tasks created successfully!');
                 
                }

      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $tids = $request->taskid;
        $data= array();
        $result = taskfollowups::where('tasksid',$tids)->orderBy('id','DESC')->get();
        //dd($result);
        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->taskstatus;
            $row[] = date('d-m-Y',strtotime($res->taskfoldate));
            $row[] = $res->tfremarks;
            if($res->tasknxtfoldate != NULL)
            {
              $row[] = date('d-m-Y',strtotime($res->tasknxtfoldate));
            }
            else
            {
                $row[] = "";
            }
           
            $row[] = $res->tfollbys;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }


    public function ajax(Request $request)
    {
          $data = $request->all();

           $taksdataid  = $request->tasksid; 

        $getid = taskfollowups::where('tasksid',$taksdataid)->update(array('fstatus' => 1));

        $result = taskfollowups::insert($data);
                
                 return response()->json(['success' => true,'message' => 'Task Followups Done successfully']);   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit($id,task $task,User $user)
    {   
        $usersdata = User::all();
        $edits = task::find($id);

        return view('superadmin.task.edit',compact('usersdata','edits'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\task  $task
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, task $task)
    {
        $updates = task::find($id);
        $updates->taskname = $request->taskname;
        $updates->tassugnto = $request->assigneto;
        $updates->tassignfrom = $request->assignfrom;
        $updates->startdate = $request->startdate;
        $updates->duedate = $request->duedate;
        $updates->save();

         return redirect('/tasks')->with('success','Tasks Updated successfully!!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,task $task)
    {
        $deles = task::find($id);
        $deles->delete();

        return redirect('/tasks')->with('success','Tasks Deleted successfully!!');

    }
}
