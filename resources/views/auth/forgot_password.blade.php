@extends('layouts.main')

@section('title', 'Reset Password')

@section('content')
<div class="container vh-100 d-flex align-items-center">
    <div class="row justify-content-center mx-auto col-md-10">
        <div class="">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Reset Password</h3>
                </div>
                <div class="card-body">
                    <form id="resetPasswordForm" method="POST" action="{{ route('password_reset_password.otp') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="form-group" style="padding-bottom: 20px">
                            <label for="new_password">New Password:</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>

                        <div class="form-group" style="padding-bottom: 20px">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div id="password-error" class="text-danger mt-2" style="display:none;">Passwords do not match</div>
                        </div>

                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#resetPasswordForm').on('submit', function(event) {
            event.preventDefault();

            var newPassword = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();

            $('#password-error').hide();

            if (newPassword !== confirmPassword) {
                $('#password-error').show();
                return;
            }

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {
                    showVanillaToast(response.message, 'success');
                    setTimeout(function() {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }, 3000);
                },
                error: function(xhr) {
                    formValidAjax(xhr);
                }
            });
        });
    });
</script>
@endsection
