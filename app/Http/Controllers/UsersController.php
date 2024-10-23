<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    protected $user;
    public function __construct(User $user){
        $this->user=$user;
    }

    public function index(Request $request) {
        $users = $this->user->getAllUsers($request);
        return view('users.index', compact('users'));
    }

    public function store(UserRequest $request)
    {
        $validatedData = [
            "email" => $request->email,
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "age" => $request->age,
        ];

        if($request->hasFile('image')) {
            $validatedData['imageUrl'] = $this->user->uploadFile($request->file('image'));
        }

        $validatedData['password'] = bcrypt($request->password);

        User::create($validatedData);

        return redirect('/users')->with('success', 'User created successfully.');
    }

    public function update(UserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $validatedData['imageUrl'] = $this->user->uploadFile($request->file('image'));
        }

        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($request->password);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        return redirect('/users')->with('success', 'User updated successfully.');
    }

    public function edit(User $user)
    {
        return response()->json($user);
    }

    public function delete(User $user)
    {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $user->delete();
        return redirect('/users')->with('success', 'User deleted successfully.');
    }
}
