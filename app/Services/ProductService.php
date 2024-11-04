<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $validatedData = [
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
                'price' => $request->price,
            ];

            $product = $this->productRepository->create($validatedData);

            if ($request->has('category')) {
                $product->categories()->sync($request->category);
            }

            DB::commit();
            return $product;
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
                'description' => 'nullable|string',
                'stock' => 'required|integer',
                'price' => 'required|numeric',
                'category' => 'array',
            ]);

            $product = $this->productRepository->find($id);

            $this->productRepository->update($product, $validatedData);
            $product->categories()->sync($request->category);

            DB::commit();
            return $product;
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
            $product = $this->productRepository->find($id);
            $this->productRepository->delete($product);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting products: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAllProducts();
    }

    public function find($id)
    {
        return $this->productRepository->find($id);
    }
}
