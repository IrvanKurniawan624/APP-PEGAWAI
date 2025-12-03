<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/icons/favicon/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/icons/favicon/favicon-16x16.png" sizes="16x16" />
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="container-fluid d-flex p-0">
        @include('layouts.sidebar')
    
        <div id="contentWrapper" class="content-main-wrapper w-100 content-expanded">
            @include('layouts.header')
            
            <div class="content pt-4 pb-5" style="box-sizing: border-box">
                <div >@yield('content')</div>
            </div>

            <footer class="app-footer bg-light d-flex align-items-center justify-content-center" style="height: 50px">
                <p class="text-center text-muted m-0">&copy; {{ date('Y') }} App Pegawai</p>
            </footer>
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
    
    @yield('modal')
    
    @yield("script")
</body>
</html>
