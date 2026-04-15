<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|max:50',
            'email'    => 'required|email|max:50|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'status'   => 'verify',
            'role_id'  => 2
        ]);

        // Kirim OTP ke email setelah register
        $otp = rand(100000, 999999);
        $user->update(['otp' => $otp]);
        \Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

        Auth::login($user);

        return redirect('/verify');
    }
}