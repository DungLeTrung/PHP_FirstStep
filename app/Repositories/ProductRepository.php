<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getAllProducts()
    {
        return $this->product->all();
    }

    public function create(array $data)
    {
        return $this->product->create($data);
    }

    public function update(Product $product, array $data)
    {
        return $product->update($data);
    }

    public function delete(Product $product)
    {
        $product->categories()->detach();
        return $product->delete();
    }

    public function find($id)
    {
        return $this->product->findOrFail($id);
    }

    public function findByName($name)
    {

        return $this->product->withTrashed()->where('name', $name)->first();
    }
}
