@extends('layouts.main')

@section('title', 'Register')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Register</h3>
                    </div>
                    <div class="card-body">

                        <form id="registerForm">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="

                                " class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="

                                "
                                    name="password_confirmation" required>
                                <div id="password-error" class="text-danger mt-2" style="display:none;">Passwords do not
                                    match</div>
                            </div>

                            <!-- First Name -->
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>

                            <!-- Last Name -->
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>

                            <!-- Age -->
                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-danger w-100">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#registerForm').on('submit', function(event) {
                    event.preventDefault();

                    $('.valid_error').html('');
                    $('#password-error').hide();

                    var formData = $(this).serialize();

                    var password = $('#password').val();
                    var confirmPassword = $('#password_confirmation').val();

                    if (password !== confirmPassword) {
                        $('#password-error').show().text("Passwords do not match.");
                        return;
                    }

                    $.ajax({
                        url: '{{ route('register' }}',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            showVanillaToast(response.message, 'success');
                            setTimeout(function() {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                                $('#registerForm')[0].reset();
                            }, 5000);
                        },
                        error: function(xhr) {
                            formValidAjax(xhr);
                        }
                    });
                });
            });
        </script>
    @endsection
