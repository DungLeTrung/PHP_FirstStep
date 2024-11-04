<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function all()
    {
        return $this->order->all();
    }

    public function find($id)
    {
        return $this->order->find($id);
    }

    public function create(array $data)
    {
        return $this->order->create($data);
    }

    public function update(Order $order, array $data)
    {
        return $order->update($data);
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}
