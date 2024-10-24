@extends('layouts.main')

@section('title', 'Send OTP')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>SEND OTP</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('send_forgot_password.OTP') }}" id="otpVerificationForm">
                            @csrf
                            <div class="form-group" style="padding-bottom: 25px">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn_send_otp">Send OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#otpVerificationForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        showVanillaToast(response.message, 'success');
                        setTimeout(function() {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
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
