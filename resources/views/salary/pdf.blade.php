<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $salary->employee->nama_lengkap }}</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { text-align: center; font-size: 13px; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 6px; }

        .bordered th, .bordered td {
            border: 1px solid #000;
        }
        .label { width: 30%; font-weight: bold; }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <div class="title">SLIP GAJI KARYAWAN</div>
    <div class="subtitle">{{ \Carbon\Carbon::parse($salary->bulan)->translatedFormat('F Y') }}</div>

    <table>
        <tr>
            <td class="label">Nama Karyawan</td>
            <td>: {{ $salary->employee->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td>: {{ $salary->employee->position->nama_jabatan }}</td>
        </tr>
    </table>

    <table class="bordered">
        <tr>
            <th>Keterangan</th>
            <th>Jumlah</th>
        </tr>

        <tr>
            <td>Gaji Pokok</td>
            <td>Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <td>Tunjangan</td>
            <td>Rp {{ number_format($salary->tunjangan, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <td>Potongan (Alpha {{ $salary->total_absence }} hari)</td>
            <td>Rp {{ number_format($salary->absence_deduction, 0, ',', '.') }}</td>
        </tr>

        <tr>
            <th>Total Gaji Diterima</th>
            <th>Rp {{ number_format($salary->final_salary, 0, ',', '.') }}</th>
        </tr>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y') }}
    </div>

</body>
</html>
