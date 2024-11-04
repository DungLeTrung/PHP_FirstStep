<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            $categories = $this->categoryService->getAllCategories();
            return view('categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching products or categories.');
        }
    }

    public function store(CategoryRequest $request)
    {
        try {
            $categories = $this->categoryService->store($request);
            if ($categories) {
                return redirect('/categories')->with('success', 'Category created successfully.');
            }
            return redirect()->back()->with('error', 'Error creating category.');
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating category.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = $this->categoryService->update($request, $id);
            if ($category) {
                return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
            }
            return redirect()->back()->with('error', 'Error updating category.');
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating category.');
        }
    }

    public function delete($id)
    {
        try {
            if ($this->categoryService->delete($id)) {
                return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
            }
            return redirect()->back()->with('error', 'Error deleting category.');
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting category.');
        }
    }

    public function edit($id)
    {
        try {
            $category = $this->categoryService->find($id);

            return view('categories.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching product or categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching product or categories.');
        }
    }
}
