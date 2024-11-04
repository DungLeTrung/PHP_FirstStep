@extends('layouts.main')

@section('title', 'Verify OTP')

@section('content')
    <div class="container vh-100 d-flex align-items-center">
        <div class="row justify-content-center mx-auto col-md-10">
            <div class="">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>CONFIRM OTP</h3>
                    </div>
                    <div class="card-body">
                        <form id="otpVerificationForm" method="POST" action="/verify-otp-forgot-password">
                            @csrf
                            <div class="form-group" style="margin-bottom: 20px; margin-top: 10px">
                                <label for="email" hidden>Email:</label>
                                <input type="hidden" id="email" name="email" value="{{ $email }}" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px; margin-top: 10px">
                                <label for="otp_code">OTP Code:</label>
                                <input type="text" class="form-control" id="otp_code" name="otp_code" required
                                    maxlength="4">
                            </div>
                            <button type="submit" class="btn btn-primary">Verify OTP</button>
                            <button type="button" class="btn btn-danger" id="resendOtpButton">Re-send OTP</button>
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

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        showVanillaToast(response.message, 'success');
                        setTimeout(function() {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }, 3000);
                        $('#otpVerificationForm')[0].reset();
                    },
                    error: function(xhr) {
                        formValidAjax(xhr);
                    }
                });
            });
        });

        $('#resendOtpButton').on('click', function() {
            window.location.href = '/send-otp';
        });
    </script>
@endsection
