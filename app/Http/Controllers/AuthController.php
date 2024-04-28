<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use APP\Models\User;
use Response;

class AuthController extends Controller
{
    public function login(Request $request) {
        $name = $request->query('name');
        
        $user = User::where('name', $name)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return response()->json(['data' => Auth::user(), 'status' => 200]);
        }

        return redirect()->back();
    }
}
