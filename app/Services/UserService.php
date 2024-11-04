<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function App\Http\uploadFile;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($request)
    {
        return $this->userRepository->getAllUsers($request);
    }

    public function createUser($request)
    {
        $validatedData = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'password' => bcrypt($request->password),
        ];

        if ($request->hasFile('image')) {
            $validatedData['imageUrl'] = uploadFile($request->file('image'));
        }

        return $this->userRepository->create($validatedData);
    }

    public function updateUser($request, $user)
    {
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

        return $this->userRepository->update($user, $validatedData);
    }

    public function deleteUser($user)
    {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        return $this->userRepository->delete($user);
    }
}
