<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    protected $category;
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $categories = $this->category->getAllCategories();
        return view('categories.index', compact('categories'));
    }

    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = [
                'name' => $request->name,
            ];
            $this->category->create($validatedData);
            DB::commit();
            return redirect('/categories')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating categories: ' . $e->getMessage());
            return redirect()->back();
        }

    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $validatedData = [
                'name' => $request->name,
            ];

            $category = $this->category->findOrFail($id);

            $category->update($validatedData);
            DB::commit();

            return redirect('/categories')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating categories: ' . $e->getMessage());
            return redirect()->back();
        }

    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $category = $this->category->findOrFail($id);

            $category->delete();
            DB::commit();

            return redirect('/categories')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting categories: ' . $e->getMessage());
            return redirect()->back();
        }

    }
}
