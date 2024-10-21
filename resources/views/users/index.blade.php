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
    @else
    <div class="alert alert-success">
        <ul>
            <li>Successfully</li>
        </ul>
    </div>
    @endif

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
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
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
                    <form id="updateUserForm" action="" method="POST">
                        @csrf
                        @method('PUT')
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
                <th scope="col">FIRST NAME</th>
                <th scope="col">LAST NAME</th>
                <th scope="col">AGE</th>
                <th scope="col">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->age }}</td>
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
        function editUser(id) {
            console.log(id)
            fetch(`/users/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('update_first_name').value = data.first_name;
                    document.getElementById('update_last_name').value = data.last_name;
                    document.getElementById('update_age').value = data.age;

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
    </script>
@endsection


