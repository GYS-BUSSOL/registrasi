<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendors/chartjs/Chart.min.css') }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/media/css/jquery.dataTables.css') }}">

    <script src="{{ asset('assets/vendors/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/media/js/jszip.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/GYSLogo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/font-awesome/css/font-awesome.min.css') }}">

    <link href="{{ asset('assets/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<style>
    /* Mark input boxes that gets an error on validation: */
    input.invalid {
        background-color: #ffdddd;
    }

    .tab {
        display: none;
    }

    /* Make circles that indicate the steps of the form: */
    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }

    .step.active {
        opacity: 1;
    }

    /* Mark the steps that are finished and valid: */
    .step.finish {
        background-color: #04AA6D;
    }
</style>

<body>
    <div id="auth" class="align-items-center justify-content-center d-flex">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-12 mx-auto">
                    <div class="card pt-4">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/images/GYSLogo.png') }}" height="55" class='mb-4'>
                                <h3>Sign In</h3>
                            </div>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <form action="{{ route('login') }}" method="post">
                                @csrf
                                <div class="form-group position-relative">
                                    <label for="username">Username</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control val-input" id="username"
                                            name="username" oninput="validateInput()" required>
                                    </div>
                                </div>
                                <div class="form-group position-relative">
                                    <div class="position-relative">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control val-input" id="password"
                                            name="password" oninput="validateInput()" required>
                                    </div>
                                </div>
                                <div class="form-group position-relative">
                                    <label for="captcha">Captcha</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control val-input" id="captcha"
                                            name="captcha" oninput="validateInput()" required>
                                    </div>
                                </div>
                                <div
                                    class="form-group d-flex position-relative align-items-center justify-content-center">
                                    <div class="position-relative captcha">
                                        <span class="mx-2">{!! captcha_img() !!}</span>
                                    </div>
                                </div>
                                <div
                                    class="form-group d-flex position-relative align-items-center justify-content-center">
                                    <div class="position-relative">
                                        <button type="button" class="btn btn-warning reload mx-2"
                                            id="reload">&#x21bb;
                                            Refresh</button>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <button type="submit" class="btn btn-primary float-end mt-4">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        $('#reload').click(function() {
            $.ajax({
                type: 'GET',
                url: 'reload-captcha',
                success: function(data) {
                    $(".captcha span").html(data.captcha)
                }
            });
        });
    </script>

    <script src="{{ asset('assets/js/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script src="{{ asset('assets/vendors/chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ asset('assets/vendors/choices.js/choices.min.js') }}"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- END THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/bootstrap-toastr/toastr.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/jquery.pulsate.min.js') }}" type="text/javascript"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
