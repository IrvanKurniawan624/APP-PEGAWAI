<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - APP PEGAWAI</title>
    <link rel="icon" type="image/png" href="/icons/favicon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/icons/favicon/favicon-16x16.png" sizes="16x16" />
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media(min-width: 1200px) {
            .container { width: 1140px !important; }
        }
    </style>
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-12 col-lg-10">
                <div class="d-flex border rounded overflow-hidden shadow-sm">
                    <div class="p-4 p-lg-5 text-center d-flex bg-base text-white align-items-center flex-fill order-md-first">
                        <div class="w-100">
                            <img src="{{ asset('icons/logo.png') }}" alt="Logo" class="logo-img mb-2" style="height: 60px;">
                            <h2>Create an Account</h2>
                            <p>Already have an account?</p>
                            <a href="{{ route('login') }}" class="btn btn-light">Sign In</a>
                        </div>
                    </div>
                    <div class="p-4 p-lg-5 flex-fill">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Register</h3>
                        </div>
                        
                        <form action="#" id="form_register">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="karyawan">Karyawan</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-dark-purple w-100 mb-3">Create Account</button>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
