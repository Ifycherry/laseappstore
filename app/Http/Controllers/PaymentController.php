<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Payment;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //
    public function createPayment(Request $request) {
        $validator = Validator::make($request->all(),[
            // 'order_ref'=> 'required|exists:order,order_ref|string',
            'order_ref'=> 'required|string',
            'total' => 'required|numeric',
            //'payment_status' => 'required|in:pending',
            'payment_ref' => 'required|string',
            'payment_method' => 'required|string',
            'address_id' => 'required|exists:addresses,id',
        ]);

        if($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Unable to make payment'
            ],400);
        }
        try{
            $customer_id = auth()->user()->id;
            $payment = new Payment;
            $payment->order_ref = $request->input('order_ref');
            $payment->total = $request->input('total');
            $payment->payment_status = 'completed';
            $payment->payment_ref = uniqid('pay_');
            $payment->payment_method = $request->input('payment_method');
            $payment->address_id = $request->input('address_id');
            $payment-> customer_id = $customer_id;
            $payment->save();

            return response()->json([
                'payment' => $payment,
                'message' => 'Payment was successful',
            ],200);

        }catch(\Exception $errors) {
            return response()->json([
                'errors' => $errors->getMessage(),
                'message'=> 'server error',
            ],500);
        }
    }
}
