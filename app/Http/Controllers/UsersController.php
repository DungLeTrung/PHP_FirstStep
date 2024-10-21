<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index() {
        $title = 'Laravel Course from Le Trung Dung';
        $x = 1;
        $y = 2;
        // return view('users.index', compact('title', 'x', 'y')); (n parameters)

        $name = 'Dung Bum';
        // return view('users.index') -> with('name', $name); //key-value (1 parameter)
        $myPhone = [
            'name' => 'Dung Bum',
            'dob' => '19/12/2002',
            'isVerified' => true
        ];
        // return view('users.index', compact('myPhone'));
        // return view('users.index',[
        //     'myPhone' => $myPhone
        // ]);
        print_r(route('users'));
        return view('users.index');

    }

    public function detail($userName, $id) {
        return "users_id = ".$id. " ,
        userName =".$userName;
        // $users = [
        //     'dung' => 'Dung',
        //     'hoang' => 'Hoang',
        // ];
        // return view('users.index',[
        //     'users' => $users[$userName] ?? 'Empty'
        // ]);
    }
}
