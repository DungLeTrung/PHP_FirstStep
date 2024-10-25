<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|array',
            'product_id.*' => 'exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
        ]);

        foreach ($validatedData['product_id'] as $index => $productId) {
        $product = Product::findOrFail($productId);
        if ($product->stock < $validatedData['quantity'][$index]) {
            return redirect()->back()->withErrors(['quantity' => 'Not enough stock for product: ' . $product->name])->withInput();
        }
    }

        $totalAmount = 0;

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_price' => 0,
        ]);

        foreach ($validatedData['product_id'] as $index => $productId) {
            $product = Product::findOrFail($productId);
            $totalAmount += $product->price * $validatedData['quantity'][$index];

            $product->stock -= $validatedData['quantity'][$index];
            $product->save();

            $order->products()->attach($productId, ['quantity' => $validatedData['quantity'][$index]]);
        }

        $order->total_price = $totalAmount;
        $order->save();

        return redirect()->back('')->with('success', 'Order created successfully and stock updated.');
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->get();
        return view('orders.index', compact('orders'));
    }
}
