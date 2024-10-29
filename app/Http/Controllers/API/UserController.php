<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function getAllUsers(Request $request)
    {
        try {
            $limit = $request->query('limit');

            if (is_null($limit)) {
                $users = $this->userModel->all();
            } else {
                $users = $this->userModel->paginate($limit);
            }
            return UserResource::collection($users);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve users.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserById($id)
    {
        try {
            $user = $this->userModel->find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user.', 'error' => $e->getMessage()], 500);
        }
    }

    public function createUser(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'email' => 'required|email|unique:users,email',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'age' => 'required|integer|min:1',
                'password' => 'required|string|min:6',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $validatedData['role'] = 'User';
            $validatedData['isVerify'] = false;

            if ($request->hasFile('image')) {
                $validatedData['imageUrl'] = $this->userModel->uploadFile($request->file('image'));
            }

            $validatedData['password'] = bcrypt($request->password);

            $user = $this->userModel->create($validatedData);
            DB::commit();

            return response()->json(['message' => 'User created successfully.', 'user' => new UserResource($user)], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create user.', 'error' => $e->getMessage()], 500);
        }
    }
}
