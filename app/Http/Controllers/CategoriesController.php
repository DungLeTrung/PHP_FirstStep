<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

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
        $validatedData = [
            'name' => $request->name,
        ];

        Category::create($validatedData);

        return redirect('/categories')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validatedData = [
            'name' => $request->name,
        ];

        $category = Category::findOrFail($id);

        $category->update($validatedData);

        return redirect('/categories')->with('success', 'Product updated successfully.');
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return redirect('/categories')->with('success', 'Product deleted successfully.');
    }
}
