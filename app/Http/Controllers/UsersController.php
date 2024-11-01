<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function App\Http\uploadFile;

class UsersController extends Controller
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $users = $this->user->getAllUsers($request);
        return view('users.index', compact('users'));
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = [
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'age' => $request->age,
            ];

            if ($request->hasFile('image')) {
                $validatedData['imageUrl'] = uploadFile($request->file('image'));
            }

            $validatedData['password'] = bcrypt($request->password);

            $this->user->create($validatedData);
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
            $validatedData = $request->validated();

            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $validatedData['imageUrl'] = uploadFile($request->file('image'));
            }

            if ($request->filled('password')) {
                $validatedData['password'] = bcrypt($request->password);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);
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
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $user->delete();
            DB::commit();
            return redirect('/users')->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting users: ' . $e->getMessage());
            return redirect()->back();
        }

    }
}
