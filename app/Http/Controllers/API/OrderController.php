<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $productModel;
    protected $userModel;
    protected $orderModel;

    public function __construct(Product $product, Order $order, User $user)
    {
        $this->productModel = $product;
        $this->orderModel = $order;
        $this->userModel = $user;
    }

    public function createOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();

            $request->validate([
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $user = $this->userModel->find($userId);
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

            // Lưu trữ sản phẩm vào session
            session(['order_products' => $request->products]);

            $order = new Order();
            $order->user_id = $userId;
            $order->status = 'PENDING';
            $order->total_price = 0;
            $order->save();

            $totalPrice = 0;

            foreach ($request->products as $product) {
                $productModel = $this->productModel->find($product['id']);

                if ($productModel->stock < $product['quantity']) {
                    return response()->json(['message' => 'Not enough stock for product: ' . $productModel['name']], 400);
                }

                $subtotal = $productModel->price * $product['quantity'];
                $totalPrice += $subtotal;

                $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);

                $productModel->stock -= $product['quantity'];
                $productModel->save();
            }

            $order->total_price = $totalPrice;
            $order->save();

            DB::commit();
            return response()->json(['message' => 'Order created successfully.', 'order' => new OrderResource($order)], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create order.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getOrder(Request $request)
    {
        try {
            $userId = Auth::id();

            $limit = $request->query('limit');

            if (is_null($limit)) {
                $orders = $this->orderModel->where('user_id', $userId)->get();
            } else {
                $orders = $this->orderModel->where('user_id', $userId)->paginate($limit);
            }
            return response()->json(
                [
                    'message' => 'Order listed successfully.',
                    'data' => OrderResource::collection($orders),
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve orders.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getOrderById($id)
    {
        try {
            $userId = Auth::id();

            $order = $this->orderModel->where('user_id', $userId)->find($id);

            if (!$order) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            return response()->json(
                [
                    'message' => 'Order found successfully.',
                    'data' => new OrderResource($order),
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve order.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();

            $order = $this->orderModel->where('user_id', $userId)->find($id);
            if (!$order) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            $request->validate([
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            // Cập nhật sản phẩm trong session
            session(['order_products' => $request->products]);

            $totalPrice = 0;

            foreach ($request->products as $product) {
                $productModel = $this->productModel->find($product['id']);

                if ($productModel->stock < $product['quantity']) {
                    return response()->json(['message' => 'Not enough stock for product: ' . $productModel['name']], 400);
                }

                $subtotal = $productModel->price * $product['quantity'];
                $totalPrice += $subtotal;

                $order->products()->syncWithoutDetaching([$product['id'] => ['quantity' => $product['quantity']]]);

                $productModel->stock -= $product['quantity'];
                $productModel->save();
            }

            $order->total_price = $totalPrice;
            $order->save();

            DB::commit();
            return response()->json(['message' => 'Order updated successfully.', 'order' => new OrderResource($order)], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update order.', 'error' => $e->getMessage()], 500);
        }
    }

    public function forgetOrder()
    {
        // Xóa sản phẩm khỏi session
        session()->forget('order_products');

        return response()->json(['message' => 'Order products cleared from session.'], 200);
    }
}
