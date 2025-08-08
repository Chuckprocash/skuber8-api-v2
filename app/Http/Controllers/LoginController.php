<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\LoginVarification;


class loginController extends Controller
{
    
    public function signin (Request $request) {

        //validate a phone number
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        //find or create the user model
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);
        if(!$user){
            return response()->json(['message' => 'Could not create user'], 401);
        }

        //send the user a one time password
        $user->login_code = rand(100000, 999999);
        $user->save();
        $user->notify(new LoginVarification());

        //return back a response

        return response()->json([
            'message' => 'Login code sent successfully',
            'user' => $user
        ], 200);
    }

    public function varifySignin(Request $request) {
        //validate the request
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        //find the user
        $user = User::where('phone', $request->phone)
        ->where('login_code', $request->login_code)
        ->first();

        //check the login code
        if($user){
        $user->update([
            'login_code' => null
        ]);
        return $user->createToken($request->login_code)->plainTextToken;
        }

        //return response if no user is found
        return response()->json(['message' => 'Invalid Varification Code'], 401);

    }

}
