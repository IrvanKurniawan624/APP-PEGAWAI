@extends('layouts.master')

@section('title','Dashboard Admin')

@section('content')
<div class="card rounded-3 bg-image w-100 bg-base mb-4" style="background-size:auto 100%;background-image:url('{{ asset('assets/img/taieri.svg') }}')">
    <div class="card-body py-5">
        <div class="d-flex align-items-center">
            <h1 class="fw-bold me-3 text-white">Welcome Back Admin !</h1>
            <span class="fw-bold text-white opacity-50">Pegawai Management App</span>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <h3 class="fw-bold">{{ $total_employee }}</h3>
                <span class="text-muted">Total Employee</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <h3 class="fw-bold">{{ $total_department }}</h3>
                <span class="text-muted">Total Department</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <h3 class="fw-bold">{{ $total_position }}</h3>
                <span class="text-muted">Total Position</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <h3 class="fw-bold">{{ $total_attendance }}</h3>
                <span class="text-muted">Total Attendance</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0" style="width: 500px">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Attendance Statistics</h5>
        <div style="height:200px">
            <canvas id="attendance_chart"></canvas>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('attendance_chart').getContext('2d');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Hadir','Sakit','Izin','Alpha'],
        datasets: [{
            data: [
                Number('{{ $stat_hadir }}'),
                Number('{{ $stat_sakit }}'),
                Number('{{ $stat_izin }}'),
                Number('{{ $stat_alpha }}')
            ],
            backgroundColor: [
                '#4CAF50','#2196F3','#FFC107','#F44336'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%'
    }
});
</script>
@endsection
