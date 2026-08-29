<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran - <?= esc($application['registration_number']) ?></title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1a202c;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #0a5c36;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0 0 4px;
            font-size: 16px;
            color: #0a5c36;
            text-transform: uppercase;
        }

        .header h3 {
            margin: 0 0 4px;
            font-size: 14px;
            color: #2d3748;
        }

        .header p {
            margin: 0;
            font-size: 10px;
            color: #718096;
        }

        .title-box {
            text-align: center;
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .title-box h1 {
            margin: 0;
            font-size: 16px;
            color: #0a5c36;
        }

        .title-box .reg-num {
            font-size: 18px;
            font-weight: bold;
            color: #1a202c;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }

        table td.label {
            width: 32%;
            color: #4a5568;
            font-weight: bold;
        }

        table td.colon {
            width: 3%;
        }

        table td.value {
            width: 65%;
            color: #1a202c;
        }

        .qr-section {
            text-align: center;
            margin: 20px 0;
        }

        .footer-note {
            background: #feebc8;
            border: 1px solid #fbd38d;
            padding: 10px;
            border-radius: 6px;
            font-size: 11px;
            color: #744210;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PENGADILAN AGAMA PENAJAM</h2>
        <h3>SI VERONIKA (Sistem Verifikasi Online CEKAdministrasi)</h3>
        <p><?= esc(get_setting('institution_address', 'Jl. Provinsi Km. 09, Nipah-Nipah, Penajam')) ?> | Telp: <?= esc(get_setting('institution_phone', '(0542) 7212345')) ?></p>
    </div>

    <div class="title-box">
        <h1>BUKTI PENDAFTARAN LAYANAN ONLINE</h1>
        <div class="reg-num"><?= esc($application['registration_number']) ?></div>
        <small style="color: #718096;">Kode Booking: <strong><?= esc($application['booking_code']) ?></strong></small>
    </div>

    <table>
        <tr>
            <td class="label">Nama Pemohon</td>
            <td class="colon">:</td>
            <td class="value"><strong><?= esc($application['applicant_name']) ?></strong></td>
        </tr>
        <tr>
            <td class="label">Nomor WhatsApp</td>
            <td class="colon">:</td>
            <td class="value"><?= esc($application['applicant_phone']) ?></td>
        </tr>
        <tr>
            <td class="label">Status Pemohon</td>
            <td class="colon">:</td>
            <td class="value"><?= esc($application['applicant_role']) ?></td>
        </tr>
        <tr>
            <td class="label">Jenis Layanan</td>
            <td class="colon">:</td>
            <td class="value"><strong><?= esc($application['service_name']) ?></strong> <?= $application['sub_service_name'] ? "({$application['sub_service_name']})" : '' ?></td>
        </tr>
        <tr>
            <td class="label">Keperluan / Pokok Permohonan</td>
            <td class="colon">:</td>
            <td class="value"><?= esc($application['subject']) ?></td>
        </tr>
        <?php if (!empty($application['case_number'])): ?>
        <tr>
            <td class="label">Nomor Perkara</td>
            <td class="colon">:</td>
            <td class="value"><?= esc($application['case_number']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="label">Jadwal Pelayanan</td>
            <td class="colon">:</td>
            <td class="value">
                <strong><?= format_indo_date($application['schedule_date']) ?></strong><br>
                Pukul <?= substr($application['schedule_start_time'], 0, 5) ?> – <?= substr($application['schedule_end_time'], 0, 5) ?> WITA
            </td>
        </tr>
        <tr>
            <td class="label">Status Permohonan</td>
            <td class="colon">:</td>
            <td class="value"><strong><?= esc($application['status']) ?></strong></td>
        </tr>
    </table>

    <div class="footer-note">
        <strong>Ketentuan Pelayanan:</strong><br>
        1. Link Zoom Meeting akan dikirimkan otomatis melalui pesan WhatsApp oleh petugas sebelum jadwal konsultasi dimulai.<br>
        2. Mohon bersiap dan bergabung 5-10 menit sebelum waktu pelayanan yang telah ditentukan.<br>
        3. Siapkan dokumen identitas diri (KTP) dan berkas pendukung saat konsultasi berlangsung.
    </div>

    <div style="margin-top: 30px; text-align: right; font-size: 11px;">
        Penajam, <?= date('d F Y') ?><br>
        <strong>Pengadilan Agama Penajam</strong>
    </div>

</body>
</html>
