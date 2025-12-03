@extends('layouts.master')

@section('title', 'Pengajuan Izin')

@section('content')

<div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 750px;">

    <h3 class="fw-bold mb-3 text-center">Pengajuan Izin / Sakit</h3>

    <!-- FORM IZIN -->
    <form id="permissionForm" class="mb-4">
        @csrf

        <label class="fw-bold mb-1">Range Tanggal</label>
        <input type="text" id="date-range" class="form-control mb-3" placeholder="Pilih tanggal">

        <input type="hidden" id="start_date" name="start_date">
        <input type="hidden" id="end_date" name="end_date">

        <label class="fw-bold mb-1">Jenis Izin</label>
        <select id="type" class="form-control mb-3">
            <option value="izin">Izin</option>
            <option value="sakit">Sakit</option>
        </select>

        <label class="fw-bold mb-1">Keterangan</label>
        <textarea id="keterangan" class="form-control mb-3" rows="3"></textarea>

        <label class="fw-bold mb-1">Attachment (opsional)</label>
        <div id="dropzoneArea" class="dropzone mb-3"></div>
        <input type="hidden" id="attachment" name="attachment">

        <button class="btn btn-dark-purple w-100 py-2 fw-bold" id="submitBtn">
            Kirim Pengajuan
        </button>
    </form>

    <hr class="my-4">

    <!-- LIST PENGAJUAN SEBELUMNYA -->
    <h5 class="fw-bold mb-3">Riwayat Pengajuan Izin</h5>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Range</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Lampiran</th>
                </tr>
            </thead>

            <tbody>
                @forelse($history as $h)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $h->start_date }} → {{ $h->end_date }}</td>
                    <td>{{ ucfirst($h->type) }}</td>
                    <td>{{ $h->keterangan ?? '-' }}</td>

                    <td>
                        @if($h->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($h->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>

                    <td>
                        @if($h->attachment)
                            <a href="{{ asset('storage/'.$h->attachment) }}" target="_blank" class="btn btn-sm btn-primary">
                                View
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada pengajuan izin</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection


@section('script')

<!-- Daterangepicker CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- Dropzone -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

<script>

// DATE RANGE PICKER
$('#date-range').daterangepicker({
    opens: 'center',
    drops: 'down',
    autoApply: true,
    linkedCalendars: false,
    alwaysShowCalendars: true,
    showDropdowns: true,
    parentEl: "body",
    locale: {
        format: 'YYYY-MM-DD',
        separator: ' - ',
        applyLabel: 'Apply',
        cancelLabel: 'Cancel'
    }
}, function(start, end){
    $('#start_date').val(start.format('YYYY-MM-DD'));
    $('#end_date').val(end.format('YYYY-MM-DD'));
});

let dr = $('#date-range').data('daterangepicker');
$('#start_date').val(dr.startDate.format('YYYY-MM-DD'));
$('#end_date').val(dr.endDate.format('YYYY-MM-DD'));


// DROPZONE
Dropzone.autoDiscover = false;

let dz = new Dropzone("#dropzoneArea", {
    url: "{{ route('permission.upload') }}",
    maxFiles: 1,
    maxFilesize: 3,
    acceptedFiles: "image/*,.pdf",
    addRemoveLinks: true,
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },

    success: function(file, res){
        $('#attachment').val(res.path);
    },

    removedfile: function(file){
        $('#attachment').val('');
        file.previewElement.remove();
    }
});


// SUBMIT FORM
$('#submitBtn').on('click', function(e){
    e.preventDefault();

    $.post("{{ route('permission.create') }}", {
        _token: "{{ csrf_token() }}",
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        type: $('#type').val(),
        keterangan: $('#keterangan').val(),
        attachment: $('#attachment').val()
    })
    .done(res => {
        Swal.fire("Success!", res.message, "success")
            .then(() => location.reload());
    })
    .fail(err => {
        Swal.fire("Error!", err.responseJSON?.message || "Something went wrong", "error");
    });
});

</script>
@endsection
