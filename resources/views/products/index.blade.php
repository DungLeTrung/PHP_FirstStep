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

    <button id="createProductBtn" class="btn btn-primary mb-3">Create Product</button>

    <!-- Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" method="POST" action="">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="productForm" class="btn btn-primary" id="submitBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    </div>


    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProductModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <form id="deleteProductForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>




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
                            <button class="btn btn-light updateProductBtn" data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}" data-description="{{ $product->description }}"
                                data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                Update
                            </button>
                            <button class="btn btn-danger deleteProductBtn" data-id="{{ $product->id }}"
                                data-bs-toggle="modal" data-bs-target="#deleteProductModal">Delete</button>
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
            $('#createProductBtn').on('click', function() {
                $('#productForm')[0].reset();
                $('#id').val('');
                $('#productModalLabel').text('Create Product');
                $('#submitBtn').text('Create');
                $('#productForm').attr('action', '/products');
                $('#productForm').attr('method', 'POST');
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'POST'
                }).appendTo('#productForm');
                $('#productModal').modal('show');
            });

            $('.updateProductBtn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var description = $(this).data('description');
                var price = $(this).data('price');
                var stock = $(this).data('stock');

                $('#id').val(id);
                $('#name').val(name);
                $('#description').val(description);
                $('#price').val(price);
                $('#stock').val(stock);

                $('#productModalLabel').text('Update Product');
                $('#submitBtn').text('Update');

                $('#productForm').attr('action', '/products/' + id);
                $('#productForm').attr('method', 'POST');
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'PUT'
                }).appendTo('#productForm');

                $('#productModal').modal('show');
            });
        });

        $(document).ready(function() {
            $('.deleteProductBtn').on('click', function() {
                var id = $(this).data('id');
                $('#deleteProductForm').attr('action', '/products/' + id);
                $('#deleteProductForm').attr('method', 'POST');
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'DELETE'
                }).appendTo('#deleteProductForm');

            });
        });


        $(document).ready(function() {
            $('#productsTable').DataTable({
                "paging": true,
                "lengthMenu": [3, 6, 9, 12],
                "pageLength": 3,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "columnDefs": [{
                    "orderable": false,
                    "targets": [5]
                }]
            });
        })
    </script>
@endsection
