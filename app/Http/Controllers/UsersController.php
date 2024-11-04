<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;

class UsersController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAllUsers($request);
        return view('users.index', compact('users'));
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();

        try {
            $this->userService->createUser($request);
            DB::commit();
            return redirect('/users')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating users: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();

        try {
            $this->userService->updateUser($request, $user);
            DB::commit();
            return redirect('/users')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating users: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function edit(User $user)
    {
        return response()->json($user);
    }

    public function delete(User $user)
    {
        DB::beginTransaction();

        try {
            $this->userService->deleteUser($user);
            DB::commit();
            return redirect('/users')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting users: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
