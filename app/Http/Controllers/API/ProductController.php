<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productModel;
    protected $categoryModel;

    public function __construct(Product $product, Category $category)
    {
        $this->productModel = $product;
        $this->categoryModel = $category;
    }

    public function getAllProducts(Request $request)
    {
        try {
            $limit = $request->query('limit');

            if (is_null($limit)) {
                $products = $this->productModel->all();
            } else {
                $products = $this->productModel->paginate($limit);
            }
            return response()->json(
                [
                    'message' => 'Product listed successfully.',
                    'data' => ProductResource::collection($products),
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve products.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getProductById($id)
    {
        try {
            $product = $this->productModel->find($id);

            if (!$product) {
                return response()->json(['message' => 'Product not found.'], 404);
            }

            return response()->json(
                [
                    'message' => 'Product found successfully.',
                    'data' => new ProductResource($product),
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve product.', 'error' => $e->getMessage()], 500);
        }
    }

    public function createProduct(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'exists:categories,id',
            ]);

            $productData = [
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'stock' => $validatedData['stock'],
            ];

            $product = $this->productModel->create($productData);

            if (!empty($validatedData['category_ids'])) {
                $product->categories()->sync($validatedData['category_ids']);
            }

            DB::commit();

            return response()->json(
                [
                    'message' => 'Product created successfully.',
                    'product' => $product,
                    'categories' => $product->categories,
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json(
                [
                    'message' => 'Failed to create product.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function updateProduct(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $product = $this->productModel->find($id);

            if (!$product) {
                return response()->json(['message' => 'Product not found.'], 404);
            }

            if ($request->has('name')) {
                $product->name = $request->name;
            }

            if ($request->has('price')) {
                $product->price = $request->price;
            }

            if ($request->has('stock')) {
                $product->stock = $request->stock;
            }

            if ($request->has('description')) {
                $product->description = $request->description;
            }

            $product->save();

            if ($request->has('category_ids')) {
                $categoryIds = $request->input('category_ids');

                $validCategories = $this->categoryModel->whereIn('id', $categoryIds)->pluck('id')->toArray();
                if (count($validCategories) !== count($categoryIds)) {
                    return response()->json(['message' => 'One or more category IDs are invalid.'], 400);
                }

                $product->categories()->sync($categoryIds);
            }

            DB::commit();
            return response()->json(['message' => 'Product updated successfully.', 'product' => new ProductResource($product)], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update product.', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteProduct($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Product not found.'], 404);
            }

            $product->delete();
            DB::commit();

            return response()->json(['message' => 'Product deleted successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete product.', 'error' => $e->getMessage()], 500);
        }
    }
}
