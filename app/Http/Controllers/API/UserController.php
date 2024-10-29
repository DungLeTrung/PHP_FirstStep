<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function changePassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8|   ',
            ]);

            $userId = Auth::id();
            $user = Auth::user();

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['message' => 'Old password is incorrect.'], 403);
            }

            $existUser = $this->userModel->find($userId);
            if (!$existUser) {
                return response()->json(['message' => 'User is not exist.'], 403);
            }

            $existUser->password = Hash::make($request->new_password);
            $existUser->save();

            DB::commit();

            return response()->json(['message' => 'Password changed successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to change password.', 'error' => $e->getMessage()], 500);
        }
    }

    public function editProfile(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'age' => 'nullable|integer|min:0',
                'imageUrl' => 'nullable|image|mimes:jpg,jpeg,png,gif,JPG,PNG|max:2048',
            ]);

            $userId = Auth::id();

            $user = $this->userModel->find($userId);
            if (!$user) {
                return response()->json(['message' => 'User is not exist.'], 403);
            }

            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->last_name;
            }

            if ($request->has('age')) {
                $user->age = $request->age;
            }

            if ($request->hasFile('imageUrl')) {
                if ($user->imageUrl) {
                    Storage::disk('public')->delete($user->imageUrl);
                }
                $user->imageUrl=$this->userModel->uploadFile($request->file('imageUrl'));
            }

            $user->save();

            DB::commit();

            return response()->json(['message' => 'Profile updated successfully.', 'user' => new UserResource($user)], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update profile.', 'error' => $e->getMessage()], 500);
        }
    }

    public function detailProfile()
    {
        try {
            $userId = Auth::id();

            $user = $this->userModel->find($userId);
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }
            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user.', 'error' => $e->getMessage()], 500);
        }
    }
}
