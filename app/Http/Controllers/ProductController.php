<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function addproduct(Request $request)
    {
        //
        $validator = Validator::make(
            $request->all(),
            [
                'product_name' => 'required|string|min:2',
                'product_category' => 'required|string|min:2',
                'initial_price' => 'required|numeric|min:1',
                'selling_price' => 'required|numeric:min:1,max:initial_price',
                'product_description' => 'required|string|min:2',
                'product_quantity' => 'required|numeric|min:1',
                'product_image' => 'required|file|mimes:jpeg,jpg,png',
                // 'vendor_id' => 'required|numeric|exists:users,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }


        try {
            $user_id = auth()->user()->id;
            $product = new Product;
            $product->product_name = $request->input('product_name');
            $product->product_category = $request->input('product_category');
            $product->initial_price = $request->input('initial_price');
            $product->selling_price = $request->input('selling_price');
            $product->product_description = $request->input('product_description');
            $product->product_quantity = $request->input('product_quantity');
            foreach ($request->file('product_image') as $image) {
                $product->product_image = $request->file('product_image')->store('product_images', 'public');
                //$product->vendor_id = $request->input('vendor_id');
            }
            $product->product_image = $request->file('product_image')->store('product_image', 'public');
            $product->vendor_id = $user_id;
            $product->save();
            return response()->json([
                'message' => 'Product added successfully',
                'product' => $product,
            ], 201);
        } catch (\Exception $error) {
            return response()->json([
                'errors' => $error,
                'message' => $error->getMessage(),
            ], 500);
        }
    }


    public function getPendingProducts()
    {
        $products = Product::where('admin_status', 'pending')->get();
        return response()->json([
            'products' => $products,
        ], 200);
    }


    public function changeProductStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'status' => 'required|in:approved,pending,rejected',
        ]);

        if($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Bad Entry',
            ],400);
        }

        try {

            
            $product = Product::where('product_id', $id)->first();
            if (!$product) {
                return response()->json([
                    'message' => 'Product not found.',
                ], 404);
            }
            Product::where('product_id', $id)->update(['admin_status' => $request->status]);
            //$product->admin_status = 'approved';
            //$product->save();
            return response()->json([
                'message'  => 'Product approved successfully.',
                'product' => $product,
            ], 200);
        } catch(\Exception $errors) {
            return response()->json([
                'message' => 'Server Error.',
                'errors' => $errors->getMessage(),
            ],500);
        }
    }



    //public function addProducts(Request )

    public function getProducts()
    {
        $products = Product::where('admin_status', 'approved')->get();
        return response()->json([
            'products' => $products,
        ], 200);
    }
    public function getAdminProducts()
    {
        $products = Product::get();
        return response()->json([
            'products' => $products,
        ], 200);
    }

    public function getProductById($id)
    {
        $product = Product::where('product_id', $id)->first();
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }
        return response()->json([
            'product' => $product,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
