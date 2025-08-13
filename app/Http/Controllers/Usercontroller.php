<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
//use Illuminate\Support\Facades\Hash;

class Usercontroller extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'firstname' => 'required|string|max:50',
                'lastname' => 'required|string|max:50',
                'email' => 'required|string|email|unique:users,email',
                // 'password' => 'required|same:confirm_password|string|min:8|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\w]{8,}$/',
                'password' => 'required|same:confirm_password|string|min:8',
                'confirm_password' => 'required|string|same:password',
                'phone_number' => 'required|string|min:11|max:14|unique:users,phone_number|regex:/^0[789][01]\d{8,}$/',
                'role' => 'required|in:user,admin,vendor',

            ],
        );

        if ($validator->fails()) {
            //    return $validator-> errors();
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Registration failed',
            ], 400);
        }
        try {
            $verification_code = rand(100000, 999999);
            $user = new User;
            $user->firstname = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->email = $request->input('email');
            $user->password = $request->input('password');
            $user->phone_number = $request->input('phone_number');
            $user->role = $request->input('role');
            $user->verification_code = $verification_code;
            $user->save();
            Mail::to($user->email)->send(new \App\Mail\UserEmailVerification($user));
            
            // if(Mail::to($user->email)->send(new \App\Mail\UserEmailVerification($user))) {
            //     $user->save();
            // } else {
            //     return response()->json([
            //      'message' => 'Mail not sent',
            //     ], 400);
            // }

            // return $user;


            return response()->json([
                'user' => $user,
                'message' => "Register Successfully: Mail sent",
            ], 201);
            // return"Register Here"
        } catch (\Exception $error) {
            return response()->json([
                'error' => $error,
                'message' => 'Server Error'
            ], 500);
        }
    }

    public function verify(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || $user->verification_code !== $request->code) {
                return response()->json([
                    'message' => 'Verification Failed',
                ], 400);
            }
            if ($user->email && $user->email === $request->email) {
                User::where('email', $user->email)->update([
                    'email_verified_at' => now(),
                    'verification_code' => null,
                ]);
            }
            $user->save();

            return response()->json([
                'message' => 'Verified Successfully',
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'errors' => $error,
            ], 500);
        }
    }


    // public function login (Request $request){
//     $validator = Validator::make($request->all(),[
//         'email' => 'required|email',
//         'password' => 'required',
//     ]);

    //     if($validator->fails()) {
//         return response()->json([
//             'errors' => $validator->errors(),
//         ],400);
//     }

    //     try{
//         $user = User::where('email',$request->input('email'))->first();
//         if($user && Hash::check($request->input('password'),
//         $user->password)) {
//           return response()->json([
//             'user' => $user,
//             'message' => 'Login Successfully',
//           ]);
//         } else{
//             return response()->json([
//                 'message' => "Invalid Login Credentials",
//             ],400);
//         }
//     } catch(\Exception $error) {
//        return response()->json([
//             'error' => $error,
//         ]);
//     }
// }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('lase-token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'message' => "Login Successfully",
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid Login Credentials'
        ], 400);
    }

    public function getUser()
    {
        $users = User::all();
        return $users;
    }

}