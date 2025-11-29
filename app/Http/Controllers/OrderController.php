<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\product;
use App\Models\Product as ModelsProduct;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    //
    public function createOrder(Request $request) {
        $validator = Validator::make($request->all(),[
            'product_id'=> 'required|exists:products,product_id|integer',
            'quantity' => 'required|integer',
            'unit_price' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'order_ref' => 'required|string',
            //'order_status' => 'required|string',
            'address_id' => 'required|exists:addresses,id',

        ]);

        if($validator->fails()) {
            return response()->json([
                'errors'=>$validator->errors(),
                'message'=> 'Unable to complete order',
            ],422);
        }
        try {
            $customer_id = auth()->user()->id;
            $order = new Order;
            $order-> product_id = $request->input('product_id');
            $order -> quantity = $request->input('quantity');
            $order -> unit_price = $request->input('unit_price');
            $order -> cost_price = $request->input('cost_price');
            $order -> order_ref = $request->input('order_ref');
            $order -> order_status = $request->input('order_status', 'pending');
            $order -> customer_id = $customer_id;
            $order -> address_id = $request->input('address_id');
            $order ->save();

            return response()->json([
                'order' => $order,
                'message' => 'Order added successfully',
            ],201);



        } catch(\Exception $errors) {
            return response()->json([
                'errors'=> $errors->getMessage(),
            ],500);
        }
    }
}
