<?php

namespace App\Http\Controllers;

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
}
