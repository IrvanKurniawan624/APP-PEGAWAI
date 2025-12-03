<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body{ font-family: sans-serif; font-size: 11px; }
        table{ width:100%; border-collapse:collapse; }
        th,td{ border:1px solid #333; padding:5px; vertical-align: top; text-align:center }
        th{ background:#f0f0f0; font-weight:bold }
        .emp-name{ text-align:left;font-weight:bold }
        .cell-box{ line-height:1.3 }
        .status{ font-weight:bold; text-transform:capitalize }

        .header {
            width: 100%;
            text-align:center;
            margin-bottom: 12px;
        }
        .header img {
            height: 50px;
            margin-bottom: 5px;
        }
        .app-title {
            font-size: 15px;
            font-weight: bold;
            margin-top: 5px;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 13px;
            margin-top: 3px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('icons/logo.png') }}" alt="Company Logo">
    <div class="app-title">PEGAWAI APP</div>
    <div class="report-title">
        Attendance Report: {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Employee</th>
            @foreach($period as $d)
                <th>{{ \Carbon\Carbon::parse($d)->format('d M') }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
    @foreach($employees as $emp)
        <tr>
            <td class="emp-name">
                {{ $emp->nama_lengkap }} <br>
                <small>Dept: {{ $emp->department ? $emp->department->nama_departmen : '-' }}</small><br>
                <small>Jabatan: {{ $emp->position ? $emp->position->nama_jabatan : '-' }}</small>
            </td>

            @foreach($period as $d)
                @php
                    $att = $map[$emp->id][$d] ?? null;
                    $status = $att ? $att['status'] : 'alpha';
                    $in  = $att && $att['check_in_time']  ? $att['check_in_time']  : '-';
                    $out = $att && $att['check_out_time'] ? $att['check_out_time'] : '-';
                @endphp

                <td>
                    <div class="cell-box">
                        <div class="status">{{ $status }}</div>
                        IN: {{ $in }} <br>
                        OUT: {{ $out }}
                    </div>
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>

</table>

</body>
</html>
