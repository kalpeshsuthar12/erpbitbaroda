<?php

namespace App\Http\Controllers;

use App\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenc = ExpenseCategory::all();

        return view('superadmin.expensecategorys.manage',compact('expenc'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.expensecategorys.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($find = ExpenseCategory::where('expensescatnames','=',$request->expensname)->first())
        {
            return redirect()->back()->with('error','Category Already Exist');
        }

        else
        {

                $ExpenseCategorymodel = new ExpenseCategory();
            $ExpenseCategory = $ExpenseCategorymodel->create([
                'expensescatnames'=> $request->expensname,
            ]);

          

            return redirect('/expense-category')->with('success','Expense Category created successfully!');
               

        }


         
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,ExpenseCategory $expenseCategory)
    {   
        $edus = ExpenseCategory::find($id);

        return view('superadmin.expensecategorys.edit',compact('edus'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, ExpenseCategory $expenseCategory)
    {
        $upda = ExpenseCategory::find($id);
        $upda->expensescatnames = $request->expensname;
        $upda->save();

        return redirect('/expense-category')->with('success','Expense Category Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExpenseCategory  $expenseCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,ExpenseCategory $expenseCategory)
    {
        $deles = ExpenseCategory::find($id);
        $deles->delete();
         return redirect('/expense-category')->with('success','Expense Category Deleted successfully!');
    }
    
     public function ajaxexpense(Request $request)
    {

                $data = $request->all();
                //dd($data);

                if(ExpenseCategory::where('expensescatnames','=',$request->expensescatnames)->first())
                {   
                    return response()->json(
                        [
                            'error' => false,
                            'message' => 'Category Already Exist!!'
                        ]
                    );


                }

                else
                {

                     $result = ExpenseCategory::insert($data);
                
                     return response()->json(
                        [
                            'success' => true,
                            'message' => 'Expense Category Add successfully'
                        ]
                    );

                }

               

    }
}
