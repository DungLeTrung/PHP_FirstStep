@extends('layouts.main')

@section('title', 'Account Management')

@section('content')
    <h1 class="text-center">LIST USERS</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        Create User
    </button>

    {{-- <div style="width: 50%">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="input-group mb-3">
                <select id="age_filter" name="age_filter" class="form-control">
                    <option value="">Filter by Age Range</option>
                    <option value="0-18">0 - 18 years</option>
                    <option value="18-30">18 - 30 years</option>
                    <option value="30-55">30 - 55 years</option>
                    <option value="55-80">55 - 80 years</option>
                </select>

                <button class="btn btn-primary" type="submit">Search</button>

                <button id="clearFilterBtn" type="button" class="btn btn-secondary ms-2">Clear Filter</button>
            </div>
        </form>
    </div> --}}

    <!-- Modal Create -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="createUserForm" action="{{ route('users.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <img id="previewImage" src="" alt="User Image" class="img-thumbnail" width="100"
                                style="display:none;">
                            <input type="file" class="form-control" id="image" name="image"
                                onchange="previewFile()">
                        </div>
                        <button type="submit" class="btn btn-success">Create User</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update -->
    <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateUserModalLabel">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateUserForm" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="update_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="update_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="update_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="update_password" name="password">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="mb-3">
                            <label for="update_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="update_first_name" name="first_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="update_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="update_last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="update_age" class="form-label">Age</label>
                            <input type="number" class="form-control" id="update_age" name="age" required>
                        </div>
                        <div class="mb-3" style="display: flex; flex-direction: column; gap: 10px">
                            <label for="update_image" class="form-label">Image</label>
                            <img id="update_previewImage" src="" alt="User Image" class="img-thumbnail"
                                width="100">
                            <input type="file" class="form-control" id="update_image" name="image"
                                accept="image/*" onchange="previewFile('update_image', 'update_previewImage')">
                        </div>
                        <button type="submit" class="btn btn-success">Update User</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>


    <!-- Modal Delete -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <form id="deleteUserForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Toasting --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="userToast" class="toast align-items-center text-white bg-primary border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">

                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="my-5">
        <table id="usersTable" class="table table-hover table-dark table-striped table-bordered my-2">
            <thead class="">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">EMAIL</th>
                    <th scope="col">FIRST NAME</th>
                    <th scope="col">LAST NAME</th>
                    <th scope="col">AGE</th>
                    <th scope="col">VERIFY</th>
                    <th scope="col">IMAGE-URL</th>
                    <th scope="col">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <th scope="row">{{ $user->id }}</th>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->age }}</td>
                        <td>
                            @if ($user->isVerify == 1)
                                <label style="color: green">VERIFIED</label>
                            @else
                                <label style="color: red">VERIFYING</label>
                            @endif
                        </td>
                        <td>
                            <img src="{{ $user->imageUrl }}" alt="User Image" width="200px" height="200px"
                                class="img-thumbnail" />
                        </td>
                        <td>
                            <div class="flex">
                                <button class="btn btn-light" onclick="editUser({{ $user->id }})">Update</button>
                                <button class="btn btn-danger" onclick="deleteUser({{ $user->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <script>
        $(document).ready(function() {
            $('#createUserForm').on('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('users.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        var toastElement = document.getElementById('userToast');
                        var toast = new bootstrap.Toast(toastElement);
                        $('.toast-body').text('User created successfully!');
                        toast.show();

                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        var toastElement = document.getElementById('userToast');
                        var toast = new bootstrap.Toast(toastElement);
                        $('.toast-body').text('Error occurred. Please try again.');
                        toast.show();
                    }
                });
            });
        });

        function editUser(id) {
            $.ajax({
                url: `/users/${id}/edit`,
                method: 'GET',
                success: function(data) {
                    $('#update_email').val(data.email);
                    $('#update_first_name').val(data.first_name);
                    $('#update_last_name').val(data.last_name);
                    $('#update_age').val(data.age);

                    const imageUrl = data.imageUrl;
                    $('#update_previewImage').attr('src', imageUrl ? imageUrl : 'path/to/default/image.png');
                    $('#updateUserForm').attr('action', `/users/${data.id}`);

                    var updateUserModal = new bootstrap.Modal(document.getElementById('updateUserModal'));
                    updateUserModal.show();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    var toastElement = document.getElementById('userToast');
                    var toast = new bootstrap.Toast(toastElement);
                    $('.toast-body').text('Error occurred. Please try again.');
                    toast.show();
                }
            });
        }

        $('#updateUserForm').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);
            var actionUrl = $(this).attr('action');

            $.ajax({
                url: actionUrl,
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    var toastElement = document.getElementById('userToast');
                    var toast = new bootstrap.Toast(toastElement);
                    $('.toast-body').text('User updated successfully!');
                    toast.show();

                    location.reload();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    var toastElement = document.getElementById('userToast');
                    var toast = new bootstrap.Toast(toastElement);
                    $('.toast-body').text('Error occurred. Please try again.');
                    toast.show();
                }
            });
        });

        function deleteUser(id) {
            document.getElementById('deleteUserForm').action = `/users/${id}`;

            var deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            deleteUserModal.show();
        }

        function previewFile(inputId, imgId) {
            const file = document.querySelector(`#${inputId}`).files[0];
            const preview = document.querySelector(`#${imgId}`);

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        }

        $(document).ready(function() {
            $('#usersTable').DataTable({
                "paging": true,
                "lengthMenu": [3, 6, 9, 10],
                "pageLength": 3,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "columnDefs": [{
                    "orderable": false,
                    "targets": [5, 6]
                }]
            });

            $(document).ready(function() {
                $('#age_filter').selectize({
                    create: false,
                    sortField: 'text'
                });
            });
        });
    </script>
@endsection
