<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Pelayanan - SI VERONIKA</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #1a202c;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0a5c36;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0 0 2px;
            font-size: 14px;
            color: #0a5c36;
            text-transform: uppercase;
        }

        .header h3 {
            margin: 0 0 2px;
            font-size: 12px;
            color: #2d3748;
        }

        .header p {
            margin: 0;
            font-size: 9px;
            color: #718096;
        }

        .summary-box {
            margin-bottom: 15px;
            background: #f7fafc;
            border: 1px solid #edf2f7;
            padding: 8px;
            border-radius: 4px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e0;
            padding: 5px 6px;
            font-size: 9px;
        }

        table.data-table th {
            background-color: #0a5c36;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PENGADILAN AGAMA PENAJAM</h2>
        <h3>LAPORAN REKAPITULASI PELAYANAN ONLINE SI VERONIKA</h3>
        <p><?= esc(get_setting('institution_address', 'Jl. Provinsi Km. 09, Penajam')) ?> | Telp: <?= esc(get_setting('institution_phone', '(0542) 7212345')) ?></p>
    </div>

    <div class="summary-box">
        <strong>Ringkasan Statistik:</strong>
        Total Permohonan: <strong><?= $reportData['stats']['total'] ?></strong> |
        Layanan Informasi: <strong><?= $reportData['stats']['layanan_informasi'] ?></strong> |
        Layanan Pendaftaran: <strong><?= $reportData['stats']['layanan_pendaftaran'] ?></strong> |
        Selesai: <strong><?= $reportData['stats']['selesai'] ?></strong> |
        Batal/Ditolak: <strong><?= $reportData['stats']['dibatalkan'] + $reportData['stats']['ditolak'] ?></strong>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>No. Registrasi</th>
                <th>Nama Pemohon</th>
                <th>NIK</th>
                <th>No. WhatsApp</th>
                <th>Layanan</th>
                <th>Jadwal Pelayanan</th>
                <th>Status</th>
                <th>Verifikator</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reportData['rows'])): ?>
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data permohonan.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($reportData['rows'] as $row): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><strong><?= esc($row['registration_number']) ?></strong></td>
                        <td><?= esc($row['applicant_name']) ?></td>
                        <td><?= esc($row['applicant_nik']) ?></td>
                        <td><?= esc($row['applicant_phone']) ?></td>
                        <td><?= esc($row['service_name']) ?></td>
                        <td><?= $row['schedule_date'] ? date('d/m/Y', strtotime($row['schedule_date'])) : '-' ?> <?= substr($row['schedule_start_time'] ?? '', 0, 5) ?> WITA</td>
                        <td style="text-align: center;"><?= esc($row['status']) ?></td>
                        <td><?= esc($row['verifier_name'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Penajam, <?= date('d F Y') ?><br>
        Mengetahui,<br>
        <strong>Pengadilan Agama Penajam</strong>
        <br><br><br><br>
        <u>( <?= esc(session()->get('user_name') ?? 'Petugas Administrator') ?> )</u>
    </div>

</body>
</html>
