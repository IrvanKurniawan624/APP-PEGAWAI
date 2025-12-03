@extends('layouts.master')

@section('title', 'Approval Izin')

@section('content')

<div class="card shadow-sm border-0 p-4">

    <h4 class="fw-bold mb-4 text-purple">Daftar Pengajuan Izin Karyawan</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle table-dark-purple">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Karyawan</th>
                    <th>Departemen</th>
                    <th>Jabatan</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Lampiran</th>
                    <th style="width:150px">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @foreach($items as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>

                <td class="fw-semibold">{{ $p->employee->nama_lengkap }}</td>

                <td>{{ $p->employee->department ? $p->employee->department->nama_departmen : '-' }}</td>

                <td>{{ $p->employee->position ? $p->employee->position->nama_jabatan : '-' }}</td>

                <td>{{ $p->start_date }} → {{ $p->end_date }}</td>

                <td><span class="fw-semibold">{{ ucfirst($p->type) }}</span></td>

                <td>{{ $p->keterangan ?? '-' }}</td>

                <td>
                    @if($p->status === 'pending')
                        <span class="badge badge-soft-warning px-3 py-2">Pending</span>
                    @elseif($p->status === 'approved')
                        <span class="badge badge-soft-success px-3 py-2">Approved</span>
                    @else
                        <span class="badge badge-soft-danger px-3 py-2">Rejected</span>
                    @endif
                </td>

                <td class="text-center">
                    @if($p->attachment)
                        <a href="{{ asset('storage/'.$p->attachment) }}" target="_blank" class="btn-view-file">
                            View
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <td>
                    @if($p->status === 'pending')
                        <button data-id="{{ $p->id }}" class="btn btn-success btn-sm btn-approve">
                            ACC
                        </button>
                        <button data-id="{{ $p->id }}" class="btn btn-danger btn-sm btn-reject">
                            Tolak
                        </button>
                    @else
                        <span class="text-muted">Selesai</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>

        </table>
    </div>
</div>

@endsection


@section('script')
<script>
$(function(){

    $('.btn-approve').click(function(){
        const id = $(this).data('id');

        Swal.fire({
            title: "ACC Pengajuan?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "ACC",
            cancelButtonText: "Batal"
        }).then(res => {
            if (!res.isConfirmed) return;

            $.post("{{ route('permission.approve') }}", {
                _token: "{{ csrf_token() }}",
                id: id
            })
            .done(r => {
                Swal.fire("Berhasil", r.message, "success")
                    .then(() => location.reload());
            });
        });
    });


    $('.btn-reject').click(function(){
        const id = $(this).data('id');

        Swal.fire({
            title: "Tolak pengajuan?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Tolak",
            cancelButtonText: "Batal"
        }).then(res => {
            if (!res.isConfirmed) return;

            $.post("{{ route('permission.reject') }}", {
                _token: "{{ csrf_token() }}",
                id: id
            })
            .done(r => {
                Swal.fire("Ditolak", r.message, "success")
                    .then(() => location.reload());
            });
        });
    });

});
</script>
@endsection
