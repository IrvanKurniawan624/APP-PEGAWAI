@extends('layouts.master')

@section('title','Attendance Overview')

@section('content')
<div class="card shadow-sm border-0 p-3">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="fw-bold">Attendance Overview</h4>

        <div class="d-flex gap-2 align-items-center">
            <input type="date" id="filter_date" class="form-control" value="{{ $date }}" style="width:170px">
            <button id="btnFilter" class="btn btn-primary px-3"><i class="fa-solid fa-filter me-2"></i>Advanced</button>
            <button id="btnExport" class="btn btn-danger px-3"><i class="fa-solid fa-file-pdf me-2"></i>Export 7d</button>
        </div>
    </div>

    <div id="filterNotice" class="alert alert-info py-2 px-3 d-none">
        Filters active
        <button id="clearFilters" class="btn btn-sm btn-outline-dark ms-2">Clear</button>
    </div>

    <div id="myMap" style="height:320px;border-radius:8px;" class="mb-3"></div>
    <table class="table table-hover align-middle datatable-attendance table-center table-dark-purple">
        <thead>
        <tr>
            <th>No</th>
            <th>Employee</th>
            <th>Role</th>
            <th>Department</th>
            <th>Date</th>
            <th>Status</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Work hours</th>
            <th>Photo</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        @foreach($records as $key => $r)
            @php
                $emp = $r->employee;
                $att = $r->attendance;

                $role = $emp && $emp->position ? $emp->position->nama_jabatan : '';
                $dept = $emp && $emp->department ? $emp->department->nama_departmen : '';

                $loc_in = $att ? $att->check_in_location : null;
                $loc_out = $att ? $att->check_out_location : null;

                $lat_in = $lng_in = $lat_out = $lng_out = null;

                if ($loc_in && strpos($loc_in, ',') !== false) {
                    $p = explode(',', $loc_in);
                    $lat_in = trim($p[0]);
                    $lng_in = trim($p[1]);
                }

                if ($loc_out && strpos($loc_out, ',') !== false) {
                    $p = explode(',', $loc_out);
                    $lat_out = trim($p[0]);
                    $lng_out = trim($p[1]);
                }
            @endphp

            <tr
                data-emp-id="{{ $emp->id }}"
                data-name="{{ $emp->nama_lengkap }}"
                data-role="{{ $role }}"
                data-dept="{{ $dept }}"
                data-date="{{ $r->date }}"
                data-status="{{ $r->status }}"
                @if($lat_in && $lng_in) data-lat="{{ $lat_in }}" data-lng="{{ $lng_in }}" @endif
                @if($lat_out && $lng_out) data-lat-out="{{ $lat_out }}" data-lng-out="{{ $lng_out }}" @endif
            >
                <td>{{ $key + 1 }}</td>
                <td class="fw-semibold">{{ $emp->nama_lengkap }}</td>
                <td>{{ $role ?: '-' }}</td>
                <td>{{ $dept ?: '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($r->date)->format('d F Y') }}</td>

                <td>
                    @php
                        $s = $r->status;
                        $cls = $s=='alpha'?'bg-danger-subtle text-danger'
                                :($s=='sakit'?'bg-warning-subtle text-warning'
                                :($s=='izin'?'bg-info-subtle text-info'
                                :'bg-success-subtle text-success'));
                    @endphp
                    <span class="badge px-3 py-2 rounded-pill {{ $cls }}">{{ $s }}</span>
                </td>

                <td>{{ $r->check_in_time ? \Carbon\Carbon::parse($r->check_in_time)->format('H:i') : '-' }}</td>
                <td>{{ $r->check_out_time ? \Carbon\Carbon::parse($r->check_out_time)->format('H:i') : '-' }}</td>

                <td>
                    @if($att && $att->total_worked_minutes)
                        @php
                            $h = floor($att->total_worked_minutes / 60);
                            $m = $att->total_worked_minutes % 60;
                        @endphp
                        {{ $h }}h {{ $m }}m
                    @else
                        -
                    @endif
                </td>

                <td>
                    <button class="btn btn-sm btn-outline-primary btn-photo"
                        data-checkin="{{ $r->check_in_photo }}"
                        data-checkout="{{ $r->check_out_photo }}"
                    >View</button>
                </td>

                <td>
                    <div class="btn-group text-center">

                        @if($r->check_in_time || $r->check_in_photo || $r->check_in_location)
                            <button class="btn btn-sm btn-danger btn-del-in" data-id="{{ $r->attendance ? $r->attendance->id : '' }}">Del IN</button>
                        @endif

                        @if($r->check_out_time || $r->check_out_photo || $r->check_out_location)
                            <button class="btn btn-sm btn-danger btn-del-out" data-id="{{ $r->attendance ? $r->attendance->id : '' }}">Del OUT</button>
                        @endif

                        <button class="btn btn-sm btn-warning btn-status" data-id="{{ $r->attendance ? $r->attendance->id : '' }}">Status</button>
                    </div>
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection

@section('modal')

<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Photos</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <h6>Check-in</h6>
                <img id="modalCheckIn" class="img-fluid mb-3" style="max-height:320px">
                <h6>Check-out</h6>
                <img id="modalCheckOut" class="img-fluid" style="max-height:320px">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFilter" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advanced Filters</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Jabatan</label>
                    <select id="filter_role" class="form-control">
                        <option value="">All</option>
                        @foreach($roles as $id => $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Department</label>
                    <select id="filter_dept" class="form-control">
                        <option value="">All</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="applyFilters" class="btn btn-primary">Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExport" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export 7-Day Period</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label>Select Start Date</label>
                <input type="date" value="{{ $date }}" id="exportRange" class="form-control" placeholder="Select date">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="doExport" class="btn btn-danger">Export PDF</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="statusSelect" class="form-control">
                    <option value="hadir">Hadir</option>
                    <option value="alpha">Alpha</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                </select>
                <input type="hidden" id="statusRowId">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="saveStatus">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(function(){

    const table = $('.datatable-attendance').DataTable({
        responsive:true,
        pageLength:10,
        lengthChange:false,
        scrollX:true,
        order:[[1,'asc']]
    });

    $('#filter_date').on('change',function(){
        const d = $(this).val();
        const params = new URLSearchParams(window.location.search);
        params.set('date', d);
        window.location.search = params.toString();
    });

    $('#btnFilter').on('click',function(){
        new bootstrap.Modal($('#modalFilter')).show();
    });

    function showFilterNotice(){ $('#filterNotice').removeClass('d-none'); }
    function hideFilterNotice(){ $('#filterNotice').addClass('d-none'); }

    $('#applyFilters').on('click',function(){
        table.column(2).search($('#filter_role').val());
        table.column(3).search($('#filter_dept').val());
        table.draw();
        bootstrap.Modal.getInstance($('#modalFilter')).hide();
        showFilterNotice();
        updatePinsFromTable();
    });

    $('#clearFilters').on('click',function(){
        $('#filter_role').val('');
        $('#filter_dept').val('');
        table.column(2).search('');
        table.column(3).search('');
        table.draw();
        hideFilterNotice();
        updatePinsFromTable();
    });

    $(document).on('click','.btn-photo',function(){
        $('#modalCheckIn').attr('src', this.dataset.checkin ? '/'+this.dataset.checkin : '/images/no-image.png');
        $('#modalCheckOut').attr('src', this.dataset.checkout ? '/'+this.dataset.checkout : '/images/no-image.png');
        new bootstrap.Modal($('#photoModal')).show();
    });

    $('#btnExport').on('click',()=>new bootstrap.Modal($('#modalExport')).show());

    $('#doExport').on('click',function(){
        const start = $('#exportRange').val();
        if(!start) return;

        const token = $('meta[name="csrf-token"]').attr('content');
        const form = $('<form method="POST" action="{{ route('attendance.export.pdf') }}"></form>');
        form.append(`<input type="hidden" name="_token" value="${token}">`);
        form.append(`<input type="hidden" name="start_date" value="${start}">`);
        $('body').append(form);
        form.submit();
    });

    $('#modalStatus').on('shown.bs.modal',function(){
        $('#statusSelect').focus();
    });

    $(document).on('click','.btn-status',function(){
        $('#statusRowId').val($(this).data('id'));
        new bootstrap.Modal($('#modalStatus')).show();
    });

    $('#saveStatus').on('click',function(){
        const id = $('#statusRowId').val();
        const status = $('#statusSelect').val();

        $.post("{{ route('attendance.update.status') }}",
            {_token:"{{ csrf_token() }}", id:id, status:status},
            ()=>location.reload()
        );
    });

    $(document).on('click', '.btn-del-in', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: "Delete Check-in?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('attendance.delete.in') }}",
                    {_token: "{{ csrf_token() }}", id: id},
                    function () {
                        Swal.fire({
                            icon: "success",
                            title: "Deleted",
                            text: "Check-in has been removed."
                        }).then(() => location.reload());
                    }
                );
            }
        });
    });


    $(document).on('click', '.btn-del-out', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: "Delete Check-out?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('attendance.delete.out') }}",
                    {_token: "{{ csrf_token() }}", id: id},
                    function () {
                        Swal.fire({
                            icon: "success",
                            title: "Deleted",
                            text: "Check-out has been removed."
                        }).then(() => location.reload());
                    }
                );
            }
        });
    });


    const map = L.map('myMap',{center:[-7.2575,112.7521],zoom:12,preferCanvas:true});
    L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{subdomains:['mt0','mt1','mt2','mt3']}).addTo(map);

    let markersLayer = L.layerGroup().addTo(map);

    const blueIcon = L.icon({
        iconUrl:"https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png",
        shadowUrl:"https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png",
        iconSize:[25,41],
        iconAnchor:[12,41],
        popupAnchor:[1,-34],
        shadowSize:[41,41]
    });

    const greenIcon = L.icon({
        iconUrl:"https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png",
        shadowUrl:"https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png",
        iconSize:[25,41],
        iconAnchor:[12,41],
        popupAnchor:[1,-34],
        shadowSize:[41,41]
    });

    function addMarkers(pins){
        markersLayer.clearLayers();

        pins.forEach(p=>{
            let lat=null, lng=null, icon=null;

            const ci = p.check_in_photo  ? '/' + p.check_in_photo  : '/images/no-image.png';
            const co = p.check_out_photo ? '/' + p.check_out_photo : '/images/no-image.png';

            let popup = "";

            if (p.lat_checkout && p.lng_checkout) {
                lat = p.lat_checkout;
                lng = p.lng_checkout;
                icon = greenIcon;

                popup = `
                    <div style="min-width:260px">

                        <b>${p.name}</b><br>
                        <small><b>Role:</b> ${p.role}</small><br>
                        <small><b>Department:</b> ${p.department}</small><br><br>

                        <div style="display:flex; gap:12px; white-space:nowrap;">
                            <div style="flex:1;">
                                <div><b>Check-in (${p.check_in_time || '-'})</b></div>
                                <img src="${ci}" style="width:100%;height:110px;object-fit:cover;border-radius:6px;">
                            </div>

                            <div style="flex:1;">
                                <div><b>Check-out (${p.check_out_time || '-'})</b></div>
                                <img src="${co}" style="width:100%;height:110px;object-fit:cover;border-radius:6px;">
                            </div>
                        </div>
                    </div>
                `;
            }

            else if (p.lat_checkin && p.lng_checkin) {
                lat = p.lat_checkin;
                lng = p.lng_checkin;
                icon = blueIcon;

                popup = `
                    <div style="min-width:240px">
                        <b>${p.name}</b><br>
                        <small><b>Role:</b> ${p.role}</small><br>
                        <small><b>Department:</b> ${p.department}</small><br><br>

                        <div style="white-space:nowrap;"><b>Check-in (${p.check_in_time || '-'})</b></div>
                        <img src="${ci}" style="width:100%;height:130px;object-fit:cover;border-radius:6px;">
                    </div>
                `;
            }

            else return;

            const m = L.marker([lat, lng], {icon});
            m.bindPopup(popup, {maxWidth: 350});
            m.addTo(markersLayer);
        });
    }


    const initialPins = @json($pins);
    addMarkers(initialPins);

    function updatePinsFromTable(){
        const visiblePins = [];
        table.rows({search:'applied'}).every(function(){
            const row = $(this.node());
            const lat_in = row.data('lat');
            const lng_in = row.data('lng');
            const lat_out = row.data('lat-out');
            const lng_out = row.data('lng-out');

            visiblePins.push({
                id:row.data('emp-id'),
                name:row.data('name'),
                role:row.data('role'),
                department:row.data('dept'),
                lat_checkin:lat_in?parseFloat(lat_in):null,
                lng_checkin:lng_in?parseFloat(lng_in):null,
                lat_checkout:lat_out?parseFloat(lat_out):null,
                lng_checkout:lng_out?parseFloat(lng_out):null,
                check_in_photo:row.find('.btn-photo').data('checkin'),
                check_out_photo:row.find('.btn-photo').data('checkout')
            });
        });
        addMarkers(visiblePins);
    }

    table.on('draw',()=>setTimeout(()=>updatePinsFromTable(),50));

});
</script>

@endsection
