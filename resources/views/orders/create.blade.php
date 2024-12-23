@extends('layouts.main')

@section('title', 'Create Order')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm" class="p-4 mb-5 border rounded bg-light shadow">
        @csrf
        <h2 class="text-center mb-4">Create Order</h2>

        <div class="mb-3">
            <label for="products" class="form-label">Select Products</label>
            <select id="products" name="product_id[]" class="form-select" multiple>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} - {{ $product->price }}$</option>
                @endforeach
            </select>
            <div class="form-text">Hold Ctrl (Windows) or Command (Mac) to select multiple products.</div>
        </div>

        <div id="quantitiesContainer" class="mb-3"></div>

        <div class="d-flex justify-content-center gap-3">
            <button type="submit" class="btn btn-primary">Place Order</button>
            <button type="button" class="btn btn-outline-success" onclick="window.location.href='{{ route('orders.index') }}'">My Orders</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsSelect = document.getElementById('products');
            const quantitiesContainer = document.getElementById('quantitiesContainer');

            productsSelect.addEventListener('change', function() {
                quantitiesContainer.innerHTML = '';

                const selectedOptions = Array.from(productsSelect.selectedOptions);
                selectedOptions.forEach(option => {
                    const productId = option.value;
                    const quantityInput = document.createElement('input');
                    quantityInput.type = 'number';
                    quantityInput.name = 'quantity[]';
                    quantityInput.className = 'form-control';
                    quantityInput.placeholder = 'Quantity for ' + option.text;
                    quantityInput.required = true;
                    quantityInput.min = 1;

                    quantitiesContainer.appendChild(quantityInput);
                });
            });
        });
    </script>
@endsection
