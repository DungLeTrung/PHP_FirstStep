<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $productService;

    public function __construct(ProductService $productService, OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->productService = $productService;
    }

    public function create()
    {
        $products = $this->productService->getAllProducts();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'product_id' => 'required|array',
                'product_id.*' => 'exists:products,id',
                'quantity' => 'required|array',
                'quantity.*' => 'integer|min:1',
            ]);
            
            $order = $this->orderService->createOrder($validatedData, auth()->id());
            if($order) {
                return redirect()->route('orders.index')->with('success', 'Order created successfully and stock updated.');
            }
            return redirect()->back()->withErrors(['error' => 'Order created unsuccessfully!!!'])->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function index()
    {
        $orders = $this->orderService->getUserOrders(auth()->id());
        return view('orders.index', compact('orders'));
    }
}
