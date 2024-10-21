<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    protected $user;
    public function __construct(User $user){
        $this->user=$user;
    }

    public function index() {
        $users = $this->user->getAllUsers();

        return view('users.index', compact('users'));
    }

    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        User::create($validatedData);

        return redirect('/users')->with('success', 'User created successfully.');
    }

    public function update(UserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        $user->update($validatedData);

        return redirect('/users')->with('success', 'User updated successfully.');
    }

    public function edit(User $user)
    {
        return response()->json($user);
    }

    public function delete(User $user)
    {
        $user->delete();
        return redirect('/users')->with('success', 'User deleted successfully.');
    }
}
