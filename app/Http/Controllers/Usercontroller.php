<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\password;

//use Illuminate\Support\Facades\Hash;

class Usercontroller extends Controller
{
    // adding the user
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
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
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
            $user->image = $request->file('image')->store('User_image', 'public');
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
                'errors' => $error->getMessage(),
                'message' => 'Server Error'
            ], 500);
        }
    }

    // verify email account
    public function verify(Request $request)
    {
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

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     try {

    //         if (Auth::attempt($credentials)) {
    //             $user = Auth::user();
    //             $token = $user->createToken('lase-token')->plainTextToken;
    //             return response()->json([
    //                 'user' => $user,
    //                 'message' => "Login Successfully",
    //                 'token' => $token,
    //             ], 200);
    //         }
    //         return response()->json([
    //             'message' => 'Invalid Login Credentials',
    //         ], 400);
    //     } catch (\Exception $error) {
    //         return response()->json([
    //             'message' => 'Invalid Login Credentials',
    //             'error' => $error->getMessage(),
    //         ], 500);
    //     }
    // }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        try {

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if ($user->email_verified_at === null) {
                        return response()->json([
                        'user' => $user,
                        'message' => "Please verify account before login",
                    ], 400);
                }
                // 'token' => $token,
                // $token = $user->createToken('lase-token')->plainTextToken;
                $token = $user->createToken('lase-token')->plainTextToken;
               return response()->json([
                'user' => $user,
                'message' => "Login Successfully",
                'token' => $token,
            ], 200);
        }

        $errors =['email' => 'Invalid email', 'password' => 'invalid password'];
        return response()->json([
            'message' => 'Invalid Login Credentials',
            'errors' => $errors,
        ],400);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Invalid Login Credentials',
                'error' => $error->getMessage(),
            ], 500);
        }
    }

    public function forgetPasswordEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
        ], ['email.exist' => 'The email does not exist in our record.']);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $user = User::where('email', $request->email)->first();
            $token = Str::random(60);
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);
            //return $token;
            // if (!$user) {
            //     return response()->json([
            //         'message' => "User not found",
            //     ], 404);
            // }
            Mail::send(
                'emails.forget-password',
                [
                    'user' => $user,
                    'url' => env('FRONTEND_URL') . '/change-password?email=' . $user->email . "&token=" . $token,
                ],
                function ($message) use ($user) {
                    $message->to($user->email)->subject('Reset Account Password');
                }
            );

            return response()->json([
                'message' => "Please check your mail to reset password"
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'errors' => $error->getMessage(),
                'message' => 'Server Error'
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:Users,email',
            'token' => 'required|string',
            'password' => 'required|string|same:confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Request Error',
                'errors' => $validator->errors(),
            ], 400);
        }
        try {
            $tokenUser = DB::table('password_reset_tokens')->where('email', $request->email)->first();
            if (!$tokenUser) {
                return response()->json([
                    'message' => 'User not Found',
                ], 422);
            } else if (!Hash::check($request->token, $tokenUser->token)) {
                return response()->json([
                    'message' => 'Token Error',
                ], 422);
            }

            $tokenDate = Carbon::parse($tokenUser->created_at);
            $diffHour = $tokenDate->diffInMinutes(now());
            if ($diffHour > 10) {
                DB::table('password_reser_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'message' => 'Token Expired',
                ], 408);
            }
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not Found',
                ], 422);
            }
            DB::table('users')->where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'Password Change Successfully',
            ], 200);
        } catch (\Exception $errors) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'Server Error',
                'errors' => $errors->getMessage(),
            ], 500);
        }
    }

    public function getUsers()
    {
        $users = User::all();
        return $users;
    }

    public function adminUpdateUserRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,admin,vendor'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
            $user->role = $request->input('role');
            $user->save();
        } catch (\Exception $error) {
            return response()->json([
                'errors' => $error,
            ], 500);
        }

        return response()->json([
            'user' => $user,
            'message' => 'User role updated successfully',
        ], 200);
    }

    public function editUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|required|string|email|unique:users,email',
            'password' => 'sometimes|required|same:confirm_password|string|min:8',
            'confirm_password' => 'sometimes|required|string|same:password',
            'phone_number' => 'sometimes|required|string|min:11|max:14|unique:users,phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Update Failed',
            ], 400);
        }
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            if ($request->has('firstname')) {
                $user->firstname = $request->input('firstname');
            }
            if ($request->has('lastname')) {
                $user->lastname = $request->input('lastname');
            }
            if ($request->has('email')) {
                $user->email = $request->input('email');
                $user->email_verified_at = null; //Reset email verification
                $verification_code = rand(100000, 999999);
                $user->verification_code = $verification_code;
                Mail::to($user->email)->send(new \App\Mail\UserEmailVerification($user));
            }
            if ($request->has('password')) {
                $user->password = $request->input('password');
            }
            if ($request->has('phone_number')) {
                $user->phone_number = $request->input('phone_number');
            }
            $user->save();

            return response()->json([
                'user' => $user,
                'message' => "User updated successfully",
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'errors' => $error,
                'message' => 'Server Error'
            ], 500);
        }
    }

    public function getUser($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }
            return response()->json([
                'user' => $user
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'error' => $error
            ], 500);
        }
    }
}
