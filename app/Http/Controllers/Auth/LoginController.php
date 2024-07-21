<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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
    protected function authenticated()
    {
        if (Auth::user()->role == '3') {
            return redirect()->route('cashier.orders.index');
        }

        // if (Auth::user()->role == '4') {
        //     return redirect()->route('drinks-orders.create');
        // }

        if (Auth::user()->role == '1') {
            return redirect()->route('home');
        }

        if (Auth::user()->role == '4') {
            return redirect()->route('select.pos');
        }



        if (Auth::user()->role == '5') {
            return redirect()->route('reservations.index');
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
