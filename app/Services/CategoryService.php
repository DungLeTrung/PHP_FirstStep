<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->all();
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $validatedData = [
                'name' => $request->name,
            ];

            $category = $this->categoryRepository->create($validatedData);

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating products: ' . $e->getMessage());
            return null;
        }
    }
    
    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $category = $this->categoryRepository->find($id);

            $this->categoryRepository->update($category, $validatedData);

            DB::commit();
            return $category;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating products: ' . $e->getMessage());
            return null;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $category = $this->categoryRepository->find($id);
            $this->categoryRepository->delete($category);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting categories: ' . $e->getMessage());
            throw $e;
        }
    }

    public function find($id)
    {
        return $this->categoryRepository->find($id);
    }
}
