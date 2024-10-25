<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index()
    {
        $products = $this->product->getAllProducts();
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function store(ProductRequest $request)
    {
        $validatedData = [
            'name' => $request->name,
            'description' => $request->description,
            'stock' => $request->stock,
            'price' => $request->price,
        ];

        $product = Product::create($validatedData);

        if ($request->has('category')) {
            $product->categories()->sync($request->category);
        }

        return redirect('/products')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
            'category' => 'array',
        ]);

        $product = Product::findOrFail($id);

        $product->update($validatedData);

        $product->categories()->sync($request->category);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        $product->categories()->detach();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function edit($id)
    {
        $product = Product::with('categories')->findOrFail($id);
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }
}
