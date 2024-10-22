@extends('layouts.main')

@section('title', 'Home Page')

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

    <form method="GET" action="{{ route('users.index') }}">
        <div class="input-group mb-3">
            <input type="text" class="form-control" name="search" placeholder="Search by first or last name" value="{{ request()->query('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
        Create User
    </button>

    <!-- Modal Create -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="createUserForm" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
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
                            <img id="previewImage" src="" alt="User Image" class="img-thumbnail" width="100" style="display:none;">
                            <input type="file" class="form-control" id="image" name="image" onchange="previewFile()">
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
    <div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true" >
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
                            <input type="text" class="form-control" id="update_first_name" name="first_name" required>
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
                            <img id="update_previewImage" src="" alt="User Image" class="img-thumbnail" width="100">
                            <input type="file" class="form-control" id="update_image" name="image" accept="image/*" onchange="previewFile('update_image', 'update_previewImage')">
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

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
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

    <table class="table table-hover table-dark table-striped table-bordered">
        <thead class="">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">EMAIL</th>
                <th scope="col">FIRST NAME</th>
                <th scope="col">LAST NAME</th>
                <th scope="col">AGE</th>
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
                    <img src="{{ $user->imageUrl }}" alt="User Image" width="200px" height="200px" class="img-thumbnail"/>
                </td>
                <td>
                    <div class="flex">
                        <button class="btn btn-light"  onclick="editUser({{ $user->id }})">Update</button>
                        <button class="btn btn-danger" onclick="deleteUser({{ $user->id }})">Delete</button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        $(document).ready(function(){
            $('#createUserForm').on('submit', function(event){
                event.preventDefault();
                console.log(123);

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('users.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        alert("User created successfully!");
                        location.reload();
                    },
                    error: function(xhr){
                        console.log(xhr.responseText);
                        alert("Error occurred. Please try again.");
                    }
                });
            });
        });
        
        function editUser(id) {
            fetch(`/users/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('update_email').value = data.email;
                    document.getElementById('update_first_name').value = data.first_name;
                    document.getElementById('update_last_name').value = data.last_name;
                    document.getElementById('update_age').value = data.age;

                    const imageUrl = data.imageUrl;
                    document.getElementById('update_previewImage').src = imageUrl ? imageUrl : 'path/to/default/image.png';
                    document.getElementById('updateUserForm').action = `/users/${data.id}`;

                    var updateUserModal = new bootstrap.Modal(document.getElementById('updateUserModal'));
                    updateUserModal.show();
                });
        }

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
    </script>
@endsection


