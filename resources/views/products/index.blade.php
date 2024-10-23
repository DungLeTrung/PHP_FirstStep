@extends('layouts.main')

@section('title', 'Product Management')

@section('content')
    <h1 class="text-center">LIST PRODUCTS</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <table id="productsTable" class="table table-hover table-dark table-striped table-bordered">
        <thead class="">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">NAME</th>
                <th scope="col">DESCRIPTION</th>
                <th scope="col">PRICE</th>
                <th scope="col">STOCK</th>
                <th scope="col">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <th scope="row">{{ $product->id }}</th>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <div class="flex">
                            <button class="btn btn-light">Update</button>
                            <button class="btn btn-danger">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No products available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
        $('#productsTable').DataTable({
            "paging": true,
            "lengthMenu": [3, 6, 9, 12],
            "pageLength": 3,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": [5] }
            ]
        });
    })
    </script>
@endsection

