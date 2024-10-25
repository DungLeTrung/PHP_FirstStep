@extends('layouts.main')

@section('title', 'Categories Management')

@section('content')
    <h1 class="text-center">LIST CATEGORIES</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button id="createCategoryBtn" class="btn btn-primary mb-3">Create Category</button>

    <!-- Modal -->
    <div class="modal fade" id="categoryModel" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm" method="POST" action="">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <div class="form-group">
                            <label for="name">Type</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="categoryForm" class="btn btn-primary" id="submitBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    </div>


    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deletecategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletecategoryModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <form id="deleteCategoryForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table id="categoriesTable" class="table table-hover table-dark table-striped table-bordered">
        <thead class="">
            <tr>
                <th scope="col" style="width: 15%">ID</th>
                <th scope="col">Name</th>
                <th scope="col" style="width: 15%">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <th scope="row">{{ $category->id }}</th>
                    <td>{{ $category->name }}</td>
                    <td>
                        <div style="display: flex; gap: 5px">
                            <button class="btn btn-light updateCategoryBtn" data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}">
                                Update
                            </button>
                            <button class="btn btn-danger deleteCategoryBtn" data-id="{{ $category->id }}"
                                data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">Delete</button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('#createCategoryBtn').on('click', function() {
                $('#categoryForm')[0].reset();
                $('#id').val('');
                $('#categoryModalLabel').text('Create Product');
                $('#submitBtn').text('Create');
                $('#categoryForm').attr('action', '/categories');
                $('#categoryForm').attr('method', 'POST');
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'POST'
                }).appendTo('#categoryForm');
                $('#categoryModel').modal('show');
            });

            $('.updateCategoryBtn').on('click', function() {
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

                $('#categoryModalLabel').text('Update Category');
                $('#submitBtn').text('Update');

                $('#categoryForm').attr('action', '/categories/' + id);
                $('#categoryForm').attr('method', 'POST');
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'PUT'
                }).appendTo('#categoryForm');

                $('#categoryModel').modal('show');
            });
        });

        $(document).ready(function() {
            $('.deleteCategoryBtn').on('click', function() {
                var id = $(this).data('id');
                $('#deleteCategoryForm').attr('action', '/categories/' + id);
                $('#deleteCategoryForm').attr('method', 'POST');
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'DELETE'
                }).appendTo('#deleteCategoryForm');

            });
        });


        $(document).ready(function() {
            $('#categoriesTable').DataTable({
                "paging": true,
                "lengthMenu": [3, 6, 9, 12],
                "pageLength": 3,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        })
    </script>
@endsection
