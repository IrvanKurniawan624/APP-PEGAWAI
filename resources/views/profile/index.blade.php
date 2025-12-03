@extends('layouts.master')

@section('title', 'Profile Saya')

@section('content')

<div class="card shadow-sm border-0 p-4 w-100">

    <h3 class="fw-bold mb-4 text-center">Profil Karyawan</h3>

    <form id="profileForm" enctype="multipart/form-data">

        @csrf

        <div class="row g-4">

            <div class="col-md-4 text-center">
                <img 
                    id="previewPhoto"
                    src="{{ $photoUrl }}"
                    class="rounded-circle shadow mb-3"
                    style="width:150px;height:150px;object-fit:cover;border:4px solid #eee"
                >
                <label class="fw-bold">Ganti Foto</label>
                <input type="file" name="photo" id="photoInput" class="form-control">
            </div>

            <div class="col-md-8">

                <div class="mb-2">
                    <label class="fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ $emp->nama_lengkap }}">
                </div>

                <div class="mb-2">
                    <label class="fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                </div>

                <div class="mb-2">
                    <label class="fw-bold">Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" class="form-control" value="{{ $emp->nomor_telepon }}">
                </div>

                <div class="mb-2">
                    <label class="fw-bold">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ $emp->tanggal_lahir }}">
                </div>

                <div class="mb-2">
                    <label class="fw-bold">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ $emp->alamat }}</textarea>
                </div>

                <div class="mb-2">
                    <label class="fw-bold">Departemen</label>
                    <select name="department_id" class="form-control">
                        <option value="">Pilih Department</option>
                        @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ $emp->department_id == $d->id ? 'selected' : '' }}>
                            {{ $d->nama_departmen }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Jabatan</label>
                    <select name="jabatan_id" class="form-control">
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions as $p)
                        <option value="{{ $p->id }}" {{ $emp->jabatan_id == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_jabatan }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <hr class="my-4">

        <h5 class="fw-bold mb-3">Ganti Password</h5>

        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="password" name="current_password" class="form-control" placeholder="Password saat ini">
            </div>
            <div class="col-md-6">
                <input type="password" name="new_password" class="form-control" placeholder="Password baru">
            </div>
        </div>

        <button id="submitProfile" class="btn btn-dark-purple w-100 py-2 fw-bold">
            Update Profile
        </button>

    </form>

</div>

@endsection

@section('script')
<script>
document.getElementById("photoInput").addEventListener("change", function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        document.getElementById("previewPhoto").src = event.target.result;
    };
    reader.readAsDataURL(file);
});

$("#profileForm").on("submit", function(e){
    e.preventDefault();

    Swal.fire({
        title: "Yakin update profil?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal"
    }).then(result => {
        if (!result.isConfirmed) return;

        let formData = new FormData(document.getElementById("profileForm"));

        const modalLoading = new bootstrap.Modal(document.getElementById("modal_loading"));
        modalLoading.show();

        $.ajax({
            url: "{{ route('profile.update.all') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(res){
                setTimeout(()=> modalLoading.hide(), 300);
                Swal.fire("Success", res.message, "success")
                    .then(()=> location.reload());
            },

            error: function(err){
                setTimeout(()=> modalLoading.hide(), 300);
                Swal.fire("Error", err.responseJSON?.message || "Gagal update", "error");
            }
        });

    });
});
</script>

@endsection
