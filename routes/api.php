<?php

use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Route::get('/user', function (Request $request) {
    //return $request->user();
//})->middleware('auth:sanctum');  
//  User Route
Route::post('/register',[Usercontroller::class, 'register']);
Route::post('/verify',[Usercontroller::class, 'verify']);
Route::post('/login',[Usercontroller::class, 'login']);
Route::post('/forgetpasswordemail', [Usercontroller::class, 'forgetPasswordEmail']);
Route::post('/changepassword', [Usercontroller::class, 'changePassword']);
// Products Route
Route::get('/allproduct',[ProductController::class, 'getProducts']);
Route::get('/product/{id}',[ProductController::class, 'getProductById']);

Route::middleware('auth:sanctum')->group(function(){
Route::get('/getuser/{id}', [Usercontroller::class, 'getUser']);
Route::get('/getusers',[Usercontroller::class, 'getUsers']);
Route::post('/addproduct',[ProductController::class, 'addproduct']);
Route::get('/pending/products',[ProductController::class,'getPendingProducts']);
Route::get('/approved/products/{id}',[ProductController::class,'approveProduct']);
Route::post('/admin/user/roleupdate/{id}',[Usercontroller::class,'adminUpdateUserRole']);
Route::post('/user/edit/{id}',[Usercontroller::class, 'editUser']);
});
// Route::get('/getusers',[Usercontroller::class, 'getusers'])->middleware
// ('auth:sanctum');

// Route::post('/addproduct',[ProductController::class, 'addproduct']);
