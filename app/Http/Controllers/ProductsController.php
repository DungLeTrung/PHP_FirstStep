<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    protected $product;
    protected $category;

    public function __construct(Product $product, Category $category)
    {
        $this->product = $product;
        $this->category = $category;

    }

    public function index()
    {
        $products = $this->product->getAllProducts();
        $categories = $this->category->all();
        return view('products.index', compact('products', 'categories'));
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = [
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
                'price' => $request->price,
            ];

            $product = $this->product->create($validatedData);

            if ($request->has('category')) {
                $product->categories()->sync($request->category);
            }
            DB::commit();
            return redirect('/products')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating products: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'stock' => 'required|integer',
                'price' => 'required|numeric',
                'category' => 'array',
            ]);

            $product = $this->product->findOrFail($id);

            $product->update($validatedData);

            $product->categories()->sync($request->category);
            DB::commit();

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating products: ' . $e->getMessage());
            return redirect()->back();
        }

    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $product = $this->product->findOrFail($id);

            $product->categories()->detach();
            $product->delete();
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting products: ' . $e->getMessage());
            return redirect()->back();
        }

    }

    public function edit($id)
    {
        $product = $this->product->with('categories')->findOrFail($id);
        $categories = $this->category->all();

        return view('products.edit', compact('product', 'categories'));
    }
}
