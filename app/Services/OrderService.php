<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $orderRepository;
    protected $productRepository;

    public function __construct(OrderRepository $orderRepository, ProductRepository $productRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    public function createOrder(array $validatedData, $userId)
    {
        DB::beginTransaction();

        try {
            $totalAmount = 0;

            $order = $this->orderRepository->create([
                'user_id' => $userId,
                'total_price' => 0,
                'status' => 'PENDING'
            ]);

            foreach ($validatedData['product_id'] as $index => $productId) {
                $product = $this->productRepository->find($productId);
                if ($product->stock < $validatedData['quantity'][$index]) {
                    throw new \Exception('Not enough stock for product: ' . $product->name);
                }

                $totalAmount += $product->price * $validatedData['quantity'][$index];

                $product->stock -= $validatedData['quantity'][$index];
                $product->save();

                $order->products()->attach($productId, ['quantity' => $validatedData['quantity'][$index]]);
            }

            $order->total_price = $totalAmount;
            $order->save();

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception('An error occurred: ' . $e->getMessage());
        }
    }

    public function getUserOrders($userId)
    {
        return $this->orderRepository->all()->where('user_id', $userId);
    }
}
