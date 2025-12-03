@extends('layouts.master')

@section('title', 'Positions')
    
@section('content')
<div class="d-flex justify-content-between">
    <h2>Table Jabatan (Positions)</h2>
    <button type="button" class="btn btn-sm btn-purple"
            data-action="form" 
            data-mode="add" 
            data-resource="positions" 
            data-target="#modal">
        <i class="fa-solid fa-plus me-2"></i>Tambah Data
    </button>
</div>
<table class="table table-center table-striped table-hover table-dark-purple mt-2 datatable">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Jabatan</th>
            <th>Gaji Pokok</th>
            <th>PotonganAbsent/Hari</th>
            <th>Updated At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($positions as $key => $position)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $position->nama_jabatan }}</td>
            <td>{{ number_format($position->gaji_pokok, 0, ',', '.') }}</td>
            <td>{{ number_format($position->potongan_per_hari, 0, ',', '.') }}</td>
            <td>{{ $position->updated_at->translatedFormat('d F Y, H:i') }}</td>
            <td>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-sm btn-shadow-info text-white"
                            data-action="form"
                            data-mode="edit"
                            data-resource="positions"
                            data-id="{{ $position->id }}">
                        <i class="fa-solid fa-pen me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm btn-shadow-danger text-white"
                            data-action="delete" 
                            data-resource="positions" 
                            data-id="{{ $position->id }}">
                        <i class="fa-solid fa-trash me-1"></i>Delete
                    </button>                       
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('modal') 
<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalLabel">Jabatan (Position)</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('positions.store') }}" class="form_submit" method="post">
                <input type="text" name="id" hidden>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="nama_jabatan">Nama Jabatan</label>
                            <input type="text" class="form-control" name="nama_jabatan" id="nama_jabatan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Potongan Per Hari (Absence)</label>
                            <input type="number" name="potongan_per_hari" class="form-control">
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <label for="gaji_pokok">Gaji Pokok (Base Salary)</label>
                            <input type="number" class="form-control" name="gaji_pokok" id="gaji_pokok">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="button_submit">
                        <span class="indicator-label">Simpan</span>
                        <div class="loading-button">
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
