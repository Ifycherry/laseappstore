<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    //
    public function createAddress(Request $request) {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'contact_name' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_verification' => 'nullable|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                'errors' =>$validator->errors(),
                'message' => 'Failed to add address',
            ],422);
        }
        try{
            $user_id = auth()->user()->id;
            $code = rand(100000, 999999);
            $address = new Address;
            $address->address= $request->input('address');
            $address->city = $request->input('city');
            $address->state = $request->input('state');
            $address->country = $request->input('country');
            $address->postal_code = $request->input('postal_code');
            $address->contact_name = $request->input('contact_name');
            $address->contact_phone = $request->input('contact_phone');
            $address->contact_verification = $code;
            $address->user_id = $user_id;
            $address->save();

            return response()->json([
                'address' => $address,
                'message' => 'Address added successfully',
            ],201);

        }catch(\Exception $error){
            return response()->json([
                'errors' => $error->getMessage(),
            ],500);
        }
    }

    public function getUserAddress() {
        $id = auth()->user()->id;
        $address = Address::where('user_id',$id)->get();
        if($address){
            return response()->json([
                'address' => $address,
                'message' => 'User address(es) found',
            ],200);
    } else{
        return response()->json([
            'message' => 'No address was found for this user',
        ],400); 
    }
}
}
