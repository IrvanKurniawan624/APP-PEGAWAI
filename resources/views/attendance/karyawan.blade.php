@extends('layouts.master')

@section('title', 'My Attendance')

@section('content')

<div class="card shadow-sm border-0 p-4 mx-auto" style="max-width: 650px;">

    <h3 class="fw-bold mb-3 text-center">Attendance Today</h3>

    <div class="text-center text-muted mb-3">
        {{ now()->format('d F Y') }}
    </div>

    <div class="row text-center mb-4">
        <div class="col">
            <div class="fw-bold">Check-In</div>
            <div class="text-primary fw-semibold">
                {{ $today?->check_in_time ? \Carbon\Carbon::parse($today->check_in_time)->format('H:i') : '-' }}
            </div>
        </div>
        <div class="col">
            <div class="fw-bold">Check-Out</div>
            <div class="text-primary fw-semibold">
                {{ $today?->check_out_time ? \Carbon\Carbon::parse($today->check_out_time)->format('H:i') : '-' }}
            </div>
        </div>
    </div>

    <hr>

    <div id="cameraSection" class="mb-3 text-center">
        <video id="camera" width="100%" height="300" autoplay playsinline class="rounded border"></video>
        <canvas id="snapshot" width="640" height="480" class="d-none"></canvas>

        <button id="captureBtn" class="btn btn-dark-purple px-4 mt-3">
            📸 Capture Photo
        </button>
    </div>

    <div id="photoSection" class="mb-3 text-center d-none">

        <img id="previewPhoto" class="img-fluid rounded shadow border mb-3"
            style="max-height: 300px;" />

        <div class="d-flex justify-content-center gap-2">
            <button id="retakeBtn" class="btn btn-outline-secondary px-4">
                Retake
            </button>
        </div>
    </div>


    <div class="mb-3">
        <label class="fw-bold">Location</label>
        <input type="text" id="locationField" class="form-control" readonly placeholder="Fetching location...">
    </div>

    <div class="d-flex gap-2">
        <button 
            id="btnCheckIn"
            class="btn btn-success flex-fill"
            {{ $today?->check_in_time ? 'disabled' : '' }}>
            Check In
        </button>

        <button 
            id="btnCheckOut"
            class="btn btn-danger flex-fill"
            {{ !$today?->check_in_time || $today?->check_out_time ? 'disabled' : '' }}>
            Check Out
        </button>
    </div>

</div>

@endsection

@section("script")
<script>
let photoBase64 = null;
let cameraStream = null;

$(document).ready(function () {

    const video = $("#camera")[0];

    function startCamera() {
        $("#cameraSection").removeClass("d-none");
        $("#photoSection").addClass("d-none");
        $("#captureBtn").removeClass("d-none");

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    cameraStream = stream;
                    video.srcObject = stream;
                })
                .catch(function (err) {
                    Swal.fire("Camera Error", "Cannot access camera: " + err.message, "error");
                });
        } else {
            Swal.fire("Camera Not Supported", "Use HTTPS or localhost.", "error");
        }
    }

    // STOP CAMERA
    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }
    }

    startCamera();

    // CAPTURE PHOTO
    $("#captureBtn").on("click", function () {
        const canvas = $("#snapshot")[0];
        const context = canvas.getContext("2d");

        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        photoBase64 = canvas.toDataURL("image/png");

        $("#previewPhoto").attr("src", photoBase64);

        stopCamera();

        $("#cameraSection").addClass("d-none");
        $("#photoSection").removeClass("d-none");
        $("#captureBtn").addClass("d-none");

        Swal.fire("Captured!", "Photo saved.", "success");
    });

    $("#retakeBtn").on("click", function () {
        photoBase64 = null;
        startCamera();
    });

    // GET LOCATION
    function fetchLocation() {
        if (!navigator.geolocation) {
            $("#locationField").val("Geolocation not supported");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const loc = pos.coords.latitude + "," + pos.coords.longitude;
                $("#locationField").val(loc);
            },
            function () {
                $("#locationField").val("Unable to get location");
            }
        );
    }
    fetchLocation();

    $("#btnCheckIn").on("click", function () {
        if (!photoBase64) {
            Swal.fire("Oops!", "You must capture and use a photo first!", "warning");
            return;
        }

        const modalLoading = new bootstrap.Modal($("#modal_loading")[0]);
        modalLoading.show();

        $.post("/attendance/check-in", {
            _token: $('meta[name="csrf-token"]').attr("content"),
            photo: photoBase64,
            location: $("#locationField").val()
        })
        .done(function (res) {
            modalLoading.hide();
            Swal.fire("Success!", res.message, "success").then(() => location.reload());
        })
        .fail(function (err) {
            modalLoading.hide();
            Swal.fire("Error!", err.responseJSON?.message || "Error", "error");
        });
    });

    $("#btnCheckOut").on("click", function () {
        if (!photoBase64) {
            Swal.fire("Oops!", "You must capture and use a photo first!", "warning");
            return;
        }

        const modalLoading = new bootstrap.Modal($("#modal_loading")[0]);
        modalLoading.show();

        $.post("/attendance/check-out", {
            _token: $('meta[name="csrf-token"]').attr("content"),
            photo: photoBase64,
            location: $("#locationField").val()
        })
        .done(function (res) {
            modalLoading.hide();
            Swal.fire("Success!", res.message, "success").then(() => location.reload());
        })
        .fail(function (err) {
            modalLoading.hide();
            Swal.fire("Error!", err.responseJSON?.message || "Error", "error");
        });
    });

});
</script>
@endsection
