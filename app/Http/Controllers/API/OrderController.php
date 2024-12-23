<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
            $userId = Auth::id() ?? User::where('role', 'Guest')->first()->id;
            $request->validate([
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $cacheKey = 'cart_guest_' . $userId;

            Cache::forget($cacheKey);

            $productsInCache = Cache::get($cacheKey, []);
            if (empty($productsInCache)) {
                $productsInCache = $request->input('products');
                Cache::put($cacheKey, $productsInCache, 60);
            }

            $user = $this->userModel->find($userId);
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }

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
            return response()->json([
                'message' => 'Order created successfully.',
                'order' => new OrderResource($order),
            ], 201);
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

    public function filterOrders(Request $request)
    {
        $filters = [
            'min_product_price' => max($request->query('min_product_price', 0), 0),
            'max_product_price' => max($request->query('max_product_price', 1000000), 0),
            'category_name' => $request->query('category_name'),
            'min_user_age' => max($request->query('min_user_age', 18), 0),
            'max_user_age' => max($request->query('max_user_age', 100), 0),
            'min_total_price' => max($request->query('min_total_price', 0), 0),
            'max_total_price' => max($request->query('max_total_price', 1000000), 0),
            'min_quantity' => max($request->query('min_quantity', 1), 0),
            'max_quantity' => max($request->query('max_quantity', 100), 0),
        ];

        try {
            $orders = $this->orderModel->filterOrders($filters);

            return response()->json(
                [
                    'message' => 'Filtered orders retrieved successfully.',
                    'data' => OrderResource::collection($orders),
                ],
                200,
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving filtered orders: ' . $e->getMessage());

            return response()->json(
                [
                    'message' => 'Failed to retrieve filtered orders.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function orderProcessing(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $status = strtoupper($request->input('status'));
            $validStatuses = ['PENDING', 'DONE', 'CANCEL'];

            if (!in_array($status, $validStatuses)) {
                return response()->json(['message' => 'Invalid order status.'], 400);
            }

            $order = $this->orderModel->find($id);

            if (!$order) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            $order->status = $status;
            $order->save();

            DB::commit();

            return response()->json(
                [
                    'message' => 'Order status updated successfully.',
                    'data' => new OrderResource($order),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error retrieving filtered orders: ' . $e->getMessage());

            return response()->json(
                [
                    'message' => 'Failed to retrieve filtered orders.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function viewGuestOrderCache()
    {
        $userId = User::where('role', 'Guest')->first()->id;
        $cacheKey = 'cart_guest_' . $userId;

        $productsInCache = Cache::get($cacheKey);

        if (empty($productsInCache)) {
            return response()->json(['message' => 'No products in guest order cache.'], 404);
        }

        return response()->json(
            [
                'message' => 'Products in guest order cache found.',
                'products' => $productsInCache,
            ],
            200,
        );
    }
}
