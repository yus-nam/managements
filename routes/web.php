<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
    // ウェブサイトのホームページ（'/'のURL）にアクセスした場合のルートです
    if (Auth::check()) {
        // ログイン状態ならば
        return redirect()->route('products.index');
        // 商品一覧ページ（ProductControllerのindexメソッドが処理）へリダイレクトします
    } else {
        // ログイン状態でなければ
        return redirect()->route('login');
        //　ログイン画面へリダイレクトします
    }
});
// もしCompanyControllerだった場合は
// companies.index のように、英語の正しい複数形になります。


Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::resource('products', ProductController::class);
});



Route::get('/list', [App\Http\Controllers\HomeController::class, 'index'])->name('list');

// //一覧画面の表示
// Route::get('/products/index/{product}', 'HomeController@index')->name('index');


// 登録フォーム表示
Route::get('/products/create/{product}', 'ProductController@create')->name('create');
    
// 登録機能
Route::post('/products/store', 'ProductController@store')->name('store');


//詳細画面の表示
Route::get('/products/show/{product}', 'ProductController@show')->name('show');


// 編集画面の表示
Route::get('/products/edit/{product}', 'ProductController@edit')->name('edit');

