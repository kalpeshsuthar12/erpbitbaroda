<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     public function login(Request $request)
    {   
        $input = $request->all();
   
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
   
        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        {
            if (auth()->user()->usercategory == 'Superadmin') 
            {
                return redirect()->route('admin.home');
            }
             else if (auth()->user()->usercategory == 'Marketing') 
            {
                return redirect()->route('marketing.home');
            }
            else if (auth()->user()->usercategory == 'Centre Manager') 
            {
                return redirect()->route('centermanager.home');
            }
            else if (auth()->user()->usercategory == 'Admin') 
            {
                return redirect()->route('subadminsse.home');
            }

            else if (auth()->user()->usercategory == 'Affiliate Marketing') 
            {
                return redirect()->route('afms.home');
            }

            else if (auth()->user()->usercategory == 'Instructor') 
            {
                return redirect()->route('faculty.home');
            }

            else if (auth()->user()->usercategory == 'Center Cordinator') 
            {
                return redirect()->route('centeercordinator.home');
            }

            else if (auth()->user()->usercategory == 'Branch') 
            {
                return redirect()->route('branchss.home');
            }

            else if (auth()->user()->usercategory == 'Past Admin') 
            {
                return redirect()->route('pastadmins.home');
            }
            else if (auth()->user()->usercategory == 'Student') 
            {
                return redirect()->route('student.home');
            }
            
           
        }
        else
        {
            return redirect()->route('login')->with('error','Email-Address And Password Are Wrong.');
        }
          
    }
}
