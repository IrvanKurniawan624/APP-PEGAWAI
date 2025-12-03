@extends('layouts.master')

@section('title', 'Slip Gaji Terbaru')

@section('content')
<div class="card shadow-sm p-3 border-0">

    <h4 class="fw-bold mb-3">Slip Gaji Terbaru</h4>

    @if(!$slip)
        <div class="alert alert-warning mb-0">
            Belum ada slip gaji tersedia.
        </div>
    @else
        <table class="table table-borderless">
            <tr>
                <th>Bulan</th>
                <td>{{ \Carbon\Carbon::parse($slip->bulan)->translatedFormat('F Y') }}</td>
            </tr>
            <tr>
                <th>Gaji Pokok</th>
                <td>Rp {{ number_format($slip->base_salary, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Tunjangan</th>
                <td>Rp {{ number_format($slip->tunjangan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Alpha</th>
                <td>{{ $slip->total_absence }} hari</td>
            </tr>
            <tr>
                <th>Potongan</th>
                <td>Rp {{ number_format($slip->absence_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-light fw-bold">
                <th>Total Gaji Diterima</th>
                <td class="text-purple">Rp {{ number_format($slip->final_salary, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="mt-3">
            <a href="{{ route('salary.pdf', $slip->id) }}"
               class="btn btn-dark-purple">
                <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
            </a>
        </div>
    @endif

</div>
@endsection
