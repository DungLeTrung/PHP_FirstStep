<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $order;
    protected $product;

    public function __construct(Order $order, Product $product)
    {
        $this->order = $order;
        $this->product = $product;
    }
    public function create()
    {
        $products = $this->product->all();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'product_id' => 'required|array',
                'product_id.*' => 'exists:products,id',
                'quantity' => 'required|array',
                'quantity.*' => 'integer|min:1',
            ]);

            foreach ($validatedData['product_id'] as $index => $productId) {
                $product = $this->product->findOrFail($productId);
                if ($product->stock < $validatedData['quantity'][$index]) {
                    return redirect()
                        ->back()
                        ->withErrors(['quantity' => 'Not enough stock for product: ' . $product->name])
                        ->withInput();
                }
            }

            $totalAmount = 0;

            $order = $this->order->create([
                'user_id' => auth()->id(),
                'total_price' => 0,
            ]);

            foreach ($validatedData['product_id'] as $index => $productId) {
                $product = $this->product->findOrFail($productId);
                $totalAmount += $product->price * $validatedData['quantity'][$index];

                $product->stock -= $validatedData['quantity'][$index];
                $product->save();

                $order->products()->attach($productId, ['quantity' => $validatedData['quantity'][$index]]);
            }

            $order->total_price = $totalAmount;
            $order->save();

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order created successfully and stock updated.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function index()
    {
        $orders = $this->order->where('user_id', auth()->id())->get();
        return view('orders.index', compact('orders'));
    }
}
