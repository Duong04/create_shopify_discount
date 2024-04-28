<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\AuthController;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\Queries\GetAllProduct;
use App\GraphQL\Queries\GetAllProducts;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ShopifyController::class, 'index'])->middleware(['verify.shopify'])->name('home');
Route::get('/rest', [ShopifyController::class, 'rest'])->name('rest');
Route::put('/rest', [ShopifyController::class, 'restApplyDiscount'])->name('rest.discount');
Route::get('/rest-rule', [ShopifyController::class, 'restRule'])->name('rest.rule');
Route::get('/rest-update-rule/{id}', [ShopifyController::class, 'showEdit'])->name('rest.show.edit');
Route::put('/rest-update-rule/{id}', [ShopifyController::class, 'restUpdateRule'])->name('rest.handle.edit');
Route::get('/graphQL', [ShopifyController::class, 'graphql'])->name('graphql');
Route::get('/graphQL-rule', [ShopifyController::class, 'graphqlRule'])->name('graphql.rule');
Route::put('/graphql', [ShopifyController::class, 'graphqlApplyDiscount'])->name('graphql.discount');
Route::get('/graphql-update-rule/{id}', [ShopifyController::class, 'graphqlShowEdit'])->name('graphql.show.edit');
Route::put('/graphql-update-rule/{id}', [ShopifyController::class, 'graphqlUpdateRule'])->name('graphql.handle.edit');

Route::get('/create', [ShopifyController::class, 'create'])->name('show.add');
Route::post('/create', [ShopifyController::class, 'store'])->name('show.store');
Route::get('/update/{id}', [ShopifyController::class, 'edit'])->name('show.edit');
Route::put('/update/{id}', [ShopifyController::class, 'update'])->name('show.update');
Route::delete('/delete/{id}', [ShopifyController::class, 'destroy'])->name('delete');
Route::post('/api/login', [AuthController::class, 'login']);