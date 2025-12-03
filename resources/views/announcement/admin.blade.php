@extends('layouts.master')

@section('title', 'Announcement Management')

@section('content')
<div class="card shadow-sm border-0 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Daftar Announcement</h4>
        <button class="btn btn-dark-purple btn-sm"
                data-action="form"
                data-mode="add"
                data-resource="announcement"
                data-target="#modal">
            <i class="fa fa-plus me-1"></i> Tambah
        </button>
    </div>

    <table class="table table-hover table-center datatable table-dark-purple">
        <thead>
            <tr>
                <th>Type</th>
                <th>Judul</th>
                <th>Pesan</th>
                <th width="120px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $a)
                <tr>
                    <td>
                        @php
                            $colors = [
                                'pemberitahuan' => 'badge-soft-info',
                                'urgent'        => 'badge-soft-danger',
                                'event'         => 'badge-soft-warning',
                                'apresiasi'     => 'badge-soft-success',
                            ];
                            $colorClass = $colors[$a->type] ?? 'badge-soft-info';
                        @endphp

                        <span class="badge {{ $colorClass }}">
                            {{ ucfirst($a->type) }}
                        </span>
                    </td>
                    <td>{{ $a->title }}</td>
                    <td>{{ Str::limit($a->message, 50) }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-shadow-info text-white"
                                data-action="form"
                                data-mode="edit"
                                data-resource="announcement"
                                data-id="{{ $a->id }}">
                            <i class="fa-solid fa-pen me-1"></i>Edit
                        </button>
                        <button class="btn btn-sm btn-shadow-danger text-white"
                                data-action="delete" 
                                data-resource="announcement" 
                                data-id="{{ $a->id }}">
                            <i class="fa-solid fa-trash me-1"></i>Delete
                        </button>  
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('modal')
<div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form class="modal-content form_submit" method="POST">
            <input type="hidden" name="id">
            <div class="modal-header bg-dark-purple">
                <h5 class="modal-title text-white" id="modalLabel">Tambah</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Type</label>
                    <select name="type" class="form-control select2">
                        <option value="pemberitahuan">Pemberitahuan</option>
                        <option value="urgent">Urgent</option>
                        <option value="event">Event</option>
                        <option value="apresiasi">Apresiasi</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Judul</label>
                    <input type="text" name="title" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Pesan</label>
                    <textarea name="message" rows="4" class="form-control"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Tutup</button>
                <button type="submit" class="btn btn-dark-purple" id="button_submit">
                    <span class="indicator-label">Simpan</span>
                    <span class="loading-button spinner-border spinner-border-sm"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
