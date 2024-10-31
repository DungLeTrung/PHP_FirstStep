@extends('layouts.main')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Login</h3>
                </div>
                <div class="card-body">
                    <form id="loginForm" method="POST" action="{{ route('login.execute') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group mb-3 ">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-secondary w-100">Login</button>
                        <div style="padding-top: 10px; text-align: center;">
                            <a style="margin-right: 10px" href="/send-otp-forgot-password" style="cursor: pointer">Forgot Password!</a>
                            <a href="{{ route('auth.google') }}" class="btn btn-danger">Login with Google</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(response) {
                    showVanillaToast(response.message, 'success');
                    setTimeout(() => {
                        window.location.href = '/';
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
