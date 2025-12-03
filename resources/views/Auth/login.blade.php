<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>APP PEGAWAI</title>
    <link rel="icon" type="image/png" href="/icons/favicon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/icons/favicon/favicon-16x16.png" sizes="16x16" />

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @media(min-width: 1200px) {
            .container {
                width: 1140px !important;
            }
        }

    </style>
</head>

<body>
    <div class="container min-vh-100 d-flex align-items-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-12 col-lg-10">
                <div class="d-flex border rounded overflow-hidden shadow-sm">
                    
                    <div class="p-4 p-lg-5 text-center d-flex bg-base text-white align-items-center flex-fill order-md-last">
                        <div class="w-100">
                            <img src="{{ asset('icons/logo.png') }}" alt="Logo" class="logo-img mb-2" style="height: 60px;">
                            <h2>Welcome to Login</h2>
                            <p>Don't have an account?</p>
                            <a href="/register" class="btn btn-light">Sign Up</a>
                        </div>
                    </div>
    
                    <div class="p-4 p-lg-5 flex-fill">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Sign In</h3>
                        </div>
    
                        <form action="#" id="form_login">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
    
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
    
                            <button type="submit" class="btn btn-dark-purple w-100 mb-3">Sign In</button>
    
                            <div class="d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">
                                        Remember Me
                                    </label>
                                </div>
    
                                <a href="#" class="text-decoration-none">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
    
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade modal-top-loader" id="modal_loading" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content" style="background-color: #F5F7F9">
                <div class="modal-body text-center p-4">
                    <img src="{{ asset('icons/loader_1.gif') }}" alt="Loading" width="200">
                    <h5 class="mt-3">Loading...</h5>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
