<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Cost Management</title>
    <meta content="Responsive admin theme build on top of Bootstrap 4" name="description"/>
    <meta content="Themesdesign" name="author" />
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/metismenu.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/style.css') }}" rel="stylesheet" type="text/css">

    <style>
        .custom-container {
            margin: auto;
            width: 520px;
            /* height: 65vh; */
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @media only screen and (max-width: 576px) {
            .custom-container {
                width: 100%;
            }
        }

        @media only screen and (min-width: 576px) and (max-width: 767px) {
            .custom-container {
                width: 100%;
            }
        }

        @media only screen and (min-width: 768px) and (max-width: 991px) {
            .custom-container {
                width: 100%;
            }
        }

        @media only screen and (min-width: 992px) and (max-width: 1199px) {
            .custom-container {
                width: 100%;
            }
        }

        .section-margin-top {
            margin-top: 0 !important;
        }

        .form-button-area {
            width: 100% !important;
        }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="accountbg"></div>
    <div class="home-btn d-none d-sm-block">
        <a href="{{ url('/') }}" class="text-white"><i class="fas fa-home h2"></i></a>
    </div>
    <div class="wrapper-page">
        <div class="card card-pages shadow-none">

            <div class="card-body">
                <h5 class="font-18 text-center">OTP Verification</h5>

                <p class="text-bold text-center" style="color: darkgray">Verification code sent to your mail address <br><b style="color: teal">{{ $userOtp->email }}</b></p>

                <div class="form-group">
                    <div class="col-12">
                        <label class="col-form-label text-center">Enter Verification Code</label>
                        <input type="number" name="match_otp" id="otp_input" class="form-control text-center" placeholder="- - - -" maxlength="6" autocomplete="off" required>
                    </div>
                </div>

                {{-- <div class="form-group text-center m-t-20">
                    <div class="col-12">
                        <button class="btn btn-primary btn-block btn-lg waves-effect waves-light" type="submit">Submit</button>
                    </div>
                </div> --}}

                <p> <span id="error_otp" class="text-danger"></span></p>

                {{-- @dd($userOtp) --}}
                <div class="form-group mt-3">

                    @if ($userOtp)
                        <div class="col-12 mt-4 verify_otp">
                            <button type="submit" id="verifyOtp" class="btn btn-primary btn-block btn-lg waves-effect waves-light justify-content-center">
                                <div id="loader" class="d-none me-2">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Verify
                            </button>
                        </div>
                    @endif

                    <div class="col-12">
                        <b class="verify_otp"></b>
                    </div>

                    <div class="position-relative">
                        <hr class="sign-up-OTP-request-line">
                        <p id="otp_timer" class="sign-up-OTP-req-text">
                            {{-- You can request OTP after <span></span> min --}}
                        </p>
                    </div>

                    <div class="col-12 mt-4">
                        <form action="{{ route('otp.resend') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $userOtp->id ?? '' }}">

                            <button type="submit" class="btn btn-primary btn-block btn-lg waves-effect waves-light justify-content-center resend_otp">
                                Resend OTP
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END wrapper -->

    <!-- jQuery  -->
    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('backend/assets/js/waves.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    <script src="https://momentjs.com/downloads/moment.min.js"></script>

    <script>
        $(document).ready(function() {
            var expirationTime = '{{ $expirationTime ?? '' }}';
            var otpTimerElement = $('#otp_timer');


            console.log(expirationTime);
            console.log(otpTimerElement);

            function updateTimer() {
                var currentTime = moment();
                // console.log(currentTime);
                var remainingTime = moment(expirationTime).diff(currentTime);

                if (remainingTime > 0) {
                    var duration = moment.duration(remainingTime);
                    var minutes = duration.minutes();
                    var seconds = duration.seconds();
                    var timerText = 'OTP expires in ' + minutes + ' minutes and ' + seconds + ' seconds';

                    otpTimerElement.text(timerText);
                    setTimeout(updateTimer, 1000); // Update the timer every second
                    $('.resend_otp').addClass('d-none')
                } else {
                    otpTimerElement.text('OTP has expired');
                    $('.resend_otp').removeClass('d-none');
                    $('.verify_otp').addClass('d-none');
                }
            }

            updateTimer();
        });


        $('#verifyOtp').click(function() {
            var otp_digit = $('#otp_input').val()
            var id = "{{ $userOtp->id }}"

            if (otp_digit != null && otp_digit != '') {

                $('#loader').removeClass('d-none')

                $.ajax({
                    url: "{{ route('check.otp.verification') }}",
                    type: 'post',
                    data: {
                        otp_digit: otp_digit,
                        id: id
                    },
                    success: function(data) {
                        $('#loader').addClass('d-none')
                        console.log(data);
                        window.location.href = '/check-email';
                    },
                    error: function(error) {
                        console.log('something went wrong', +error);
                        $('#error_otp').html('Not Matched!');
                    }
                });
            } else {
                $('#error_otp').html('Please input your valid otp');
            }
        });
    </script>
</body>

</html>
