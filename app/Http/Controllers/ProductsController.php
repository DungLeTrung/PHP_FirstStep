<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            $products = $this->productService->getAllProducts();
            $categories = $this->categoryService->getAllCategories();
            return view('products.index', compact('products', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching products or categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching products or categories.');
        }
    }

    public function store(ProductRequest $request)
    {
        try {
            $product = $this->productService->store($request);
            if ($product) {
                return redirect('/products')->with('success', 'Product created successfully.');
            }
            return redirect()->back()->with('error', 'Error creating product.');
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating product.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = $this->productService->update($request, $id);
            if ($product) {
                return redirect()->route('products.index')->with('success', 'Product updated successfully.');
            }
            return redirect()->back()->with('error', 'Error updating product.');
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating product.');
        }
    }

    public function delete($id)
    {
        try {
            if ($this->productService->delete($id)) {
                return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
            }
            return redirect()->back()->with('error', 'Error deleting product.');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting product.');
        }
    }

    public function edit($id)
    {
        try {
            $product = $this->productService->find($id);
            $categories = $this->categoryService->getAllCategories();

            return view('products.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching product or categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching product or categories.');
        }
    }
}
