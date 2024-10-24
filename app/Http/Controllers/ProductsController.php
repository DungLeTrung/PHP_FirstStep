<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected $product;
    public function __construct(Product $product){
        $this->product=$product;
    }

    public function index() {
        $products = $this->product->getAllProducts();
        return view('products.index', compact('products'));
    }

    public function store(ProductRequest $request)
    {
        $validatedData = [
            "name" => $request->name,
            "description" => $request->description,
            "stock" => $request->stock,
            "price" => $request->price,
        ];

        Product::create($validatedData);

        return redirect('/products')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validatedData = [
            "name" => $request->name,
            "description" => $request->description,
            "stock" => $request->stock,
            "price" => $request->price,
        ];

        $product = Product::findOrFail($id);

        $product->update($validatedData);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function delete($id) {
        $product = Product::findOrFail($id);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
