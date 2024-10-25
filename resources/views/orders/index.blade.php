@extends('layouts.main')

@section('title', 'My Orders')

@section('content')
    <h1 class="text-center">My Orders</h1>

    @if ($orders->isEmpty())
        <p class="text-center">You have no orders yet.</p>
    @else
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->total_price }}$</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ $order->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
