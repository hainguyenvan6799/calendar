<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Sendcode;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    function login(Request $request)
    {   
        if($this->hasTooManyLoginAttempts($request))
        {
            $this->fireLockoutResponse($request);
        }
        //------------------------------
        if($this->guard()->validate($this->credentials($request)))
        {
            $user = $this->guard()->getLastAttempted();
            if($user->active && Auth::attempt(['email'=>$request->email, 'password'=>$request->password]))
            {
                return $this->sendLoginResponse($request);
            }
        
            else
            {
                $this->incrementLoginAttempts($request);
                $user->code = Sendcode::sendCode($user->phone);
                if($user->save())
                {
                    return redirect('verify?phone='.$user->phone);
                }
            }

        // if(!Auth::attempt(['email'=>$request->email, 'password'=>$request->password, 'active'=>1]))
        // {
        //     $this->incrementLoginAttempts($request);
        //     $user->code = Sendcode::sendCode($user->phone);
        //     if($user->save())
        //     {
        //         return redirect('verify?phone='.$user->phone);
        //     }
        // }
        // else
        // {
        //     return $this->sendLoginResponse($request);
        // }
        //------------------------------------
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
}
}
