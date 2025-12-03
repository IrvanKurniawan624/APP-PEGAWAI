@extends('layouts.master')

@section('title', 'Kelola Gaji Pegawai')

@section('content')
@php
    use Carbon\Carbon;
    $now = Carbon::now();
    $months = [];
    for ($i = 0; $i < 12; $i++) {
        $m = $now->copy()->subMonths($i);
        $months[] = $m->format('Y-m');
    }
@endphp

<div class="card shadow-sm p-3 border-0">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Kelola Slip Gaji</h4>

        <div class="d-flex gap-2 align-items-center">
            <label class="mb-0 me-2 fw-bold">Bulan</label>
            <select id="select_bulan" class="form-control" style="width:160px">
                @foreach($months as $m)
                    <option value="{{ $m }}" {{ $m === $now->format('Y-m') ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-purple" id="btn_refresh">
                <i class="fa-solid fa-sync me-1"></i> Refresh
            </button>
        </div>
    </div>

    <table class="table table-center table-dark-purple table-hover datatable" id="tableEmployees">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Gaji Pokok</th>
                <th>Status Slip</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($employees as $e)
                @php
                    $existsDefault = \App\Models\Salary::where('employee_id', $e->id)
                        ->where('bulan', $now->format('Y-m'))
                        ->exists();
                @endphp
                <tr data-employee-id="{{ $e->id }}">
                    <td>{{ $e->nama_lengkap }}</td>
                    <td>{{ $e->position->nama_jabatan ?? '-' }}</td>
                    <td>Rp {{ number_format($e->position->gaji_pokok, 0, ',', '.') }}</td>
                    <td>
                        @if($existsDefault)
                            <span class="badge bg-success">Sudah ada ({{ $now->format('Y-m') }})</span>
                        @else
                            <span class="badge bg-secondary">Belum</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-shadow-info text-white"
                                    onclick="openSalaryModal({{ $e->id }})">
                                Kelola
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</div>

{{-- Modal Invoice / Preview --}}
<div class="modal fade" id="salaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="salaryForm">
            @csrf
            <div class="modal-header bg-dark-purple">
                <h5 class="text-white">Preview Slip Gaji</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="employee_id" name="employee_id">

                <div class="row">
                    <div class="col-md-6 text-center">
                        <h5 id="empName" class="fw-bold mb-1"></h5>
                        <div id="empPosition" class="opacity-75 mb-2"></div>
                        <div class="small text-muted">Bulan: <span id="preview_bulan"></span></div>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Gaji Pokok</th>
                                <td id="inv_gaji">-</td>
                            </tr>
                            <tr>
                                <th>Tunjangan (input)</th>
                                <td>
                                    <input type="number" id="tunjangan" class="form-control" value="0" min="0">
                                </td>
                            </tr>
                            <tr>
                                <th>Total Alpha</th>
                                <td id="inv_alpha">-</td>
                            </tr>
                            <tr>
                                <th>Potongan</th>
                                <td id="inv_potongan">-</td>
                            </tr>
                            <tr class="table-light fw-bold">
                                <th>Total Gaji Diterima</th>
                                <td id="inv_final" class="text-dark-purple">-</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div id="invoice_notes" class="mt-3 small text-muted">
                    * Preview dihitung berdasarkan data absensi pada bulan terpilih. Generate akan menyimpan slip ke database.
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-dark-purple" id="btn_generate">
                    <i class="fa-solid fa-file-circle-plus me-1"></i> Generate Slip
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')<script>
function openSalaryModal(employeeId) {}

$(document).ready(function () {
    const modal = new bootstrap.Modal($('#salaryModal')[0]);

    function rupiah(n) {
        n = parseInt(n || 0);
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function monthLabel(m) {
        return new Date(m + "-01").toLocaleString('default', { month: 'long', year: 'numeric' });
    }

    function preview(employeeId, bulan, tunjangan = 0) {
        return $.post("{{ route('salary.preview') }}", {
            _token: $('meta[name="csrf-token"]').attr('content'),
            employee_id: employeeId,
            bulan: bulan,
            tunjangan: tunjangan
        });

    }

    window.openSalaryModal = function (employeeId) {
        const bulan = $('#select_bulan').val();
        $('#employee_id').val(employeeId);
        $('#tunjangan').val(0);
        $('#preview_bulan').text(monthLabel(bulan));

        preview(employeeId, bulan).done(function (res) {
            const d = res.data;

            $('#empName').text(d.nama);
            $('#empPosition').text(d.jabatan);
            $('#inv_gaji').text('Rp ' + rupiah(d.gaji));
            $('#inv_alpha').text(d.alpha + ' hari');
            $('#inv_potongan').text('Rp ' + rupiah(d.potongan));
            $('#inv_final').text('Rp ' + rupiah(d.final));

            $('#tunjangan').off('input').on('input', function () {
                const tun = $(this).val() || 0;

                preview(employeeId, bulan, tun).done(function (resp) {
                    const x = resp.data;
                    $('#inv_potongan').text('Rp ' + rupiah(x.potongan));
                    $('#inv_final').text('Rp ' + rupiah(x.final));
                });
            });

            modal.show();
        });
    };

    $('#btn_generate').on('click', function () {
        const empId = $('#employee_id').val();
        const bulan = $('#select_bulan').val();
        const tunjangan = $('#tunjangan').val() || 0;

        Swal.fire({
            title: 'Generate Slip?',
            text: 'Slip akan dibuat dan muncul di karyawan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed) return;

            $.post("{{ route('salary.generate') }}", {
                _token: $('meta[name="csrf-token"]').attr('content'),
                employee_id: empId,
                bulan: bulan,
                tunjangan: tunjangan
            }).done(function (res) {
                Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
            });
        });
    });

    $('#btn_refresh').on('click', function () {
        location.reload();
    });
});
</script>

@endsection
