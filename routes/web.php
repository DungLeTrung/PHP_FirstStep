<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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

Route::get('users', [
    UsersController::class,
    'index'
])->name('users');

Route::get('users/{productName}/{id}', [
    UsersController::class,
    'detail'
]) -> where([
    'id' => '[0-9]+',
    'productName' => '[a-zA-Z0-9]+'
]);

// Route::get('/', function () {
//     return view('home');
// });

// Route::get('/user', function () {
//     return 'This is the users page';
// });

// //Response an array
// Route::get('/foods', function () {
//     return [
//         'sushi', 'sashimi', 'tofu'
//     ];
// });

// //Response an object
// Route::get('/aboutMe', function () {
//     return response() -> json([
//         'name' => 'Le Trung Dung',
//         'age' => 18,
//         'email' => 'le_trung_dung@gmail.com',
//     ]);
// });

// //Response another request = redirect to
// Route::get('/something', function () {
//     return redirect('/');
// });


