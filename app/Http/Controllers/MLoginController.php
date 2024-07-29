<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MLoginController extends Controller
{
    public function authenticate(Request $request)
    {
        
        $user = User::where('password', '=', $request->password)->first();
        if ($user) {
            if(Auth::loginUsingId($user->id)){
                if ($user->role == 4) {
                    return redirect()->route('select.pos');
                } if ($user->role == 3) {
                    return redirect()->route('cashier.orders.index');
                } else {
                    return redirect()->route('home');
                }
            }
        } else {
            return redirect()->route('login');
        }

       
    }
}
