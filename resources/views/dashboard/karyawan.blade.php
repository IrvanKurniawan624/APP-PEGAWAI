@extends('layouts.master')

@section('title', 'Dashboard Pegawai')

@section('content')

<div class="card rounded-3 bg-image w-100 bg-base"
     style="background-size: auto 100%; background-image: url(assets/img/taieri.svg)">
    <div class="card-body py-5">
        <div class="d-flex align-items-center">
            <h1 class="fw-bold me-3 text-white">Welcome Back, {{ Auth::user()->name }}!</h1>
            <span class="fw-bold text-white opacity-50">Pegawai Management App</span>
        </div>
    </div>
</div>

<div class="mt-4">

    <h2>Announcement</h2>
    @foreach ($announcements as $a)

        @php
            $map = [
                'pemberitahuan' => ['info', 'fa-circle-info'],
                'urgent'        => ['danger', 'fa-triangle-exclamation'],
                'event'         => ['warning', 'fa-calendar-days'],
                'apresiasi'     => ['success', 'fa-handshake-angle'],
            ];

            $alertClass = $map[$a->type][0] ?? 'info';
            $icon       = $map[$a->type][1] ?? 'fa-circle-info';
        @endphp

        <div class="alert alert-{{ $alertClass }} alert-dismissible fade show shadow-sm d-flex align-items-start gap-3"
             role="alert">

            <i class="fa-solid {{ $icon }} fs-4 mt-1"></i>

            <div>
                <h6 class="fw-bold mb-1">{{ $a->title }}</h6>
                <p class="mb-0">{{ $a->message }}</p>
            </div>

            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

    @endforeach

</div>

@endsection
