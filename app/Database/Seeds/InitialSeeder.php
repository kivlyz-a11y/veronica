<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Seed Users
        $users = [
            [
                'name'          => 'Super Administrator',
                'email'         => 'superadmin@pa-penajam.go.id',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'          => 'superadmin',
                'phone'         => '08115420001',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Admin Pelayanan',
                'email'         => 'admin@pa-penajam.go.id',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'          => 'admin',
                'phone'         => '08115420002',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Ahmad Fauzi, S.H. (Petugas PTSP Online)',
                'email'         => 'petugas@pa-penajam.go.id',
                'password_hash' => password_hash('petugas123', PASSWORD_DEFAULT),
                'role'          => 'officer',
                'phone'         => '08115420003',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'Drs. H. M. Said, M.H. (Ketua PA Penajam)',
                'email'         => 'pimpinan@pa-penajam.go.id',
                'password_hash' => password_hash('pimpinan123', PASSWORD_DEFAULT),
                'role'          => 'pimpinan',
                'phone'         => '08115420004',
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($users as $u) {
            $exists = $db->table('users')->where('email', $u['email'])->countAllResults() > 0;
            if (!$exists) {
                $db->table('users')->insert($u);
            }
        }

        // 2. Seed Services & Subcategories
        $service1 = [
            'name'        => 'Layanan Informasi',
            'slug'        => 'layanan-informasi',
            'description' => 'Konsultasi dan permintaan informasi perkara, administrasi perkara, jadwal sidang, e-Court, dan prosedur peradilan.',
            'icon'        => 'bi-info-circle-fill',
            'status'      => 'active',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        
        $s1 = $db->table('services')->where('slug', 'layanan-informasi')->get()->getRowArray();
        if (!$s1) {
            $db->table('services')->insert($service1);
            $service1Id = $db->insertID();
        } else {
            $service1Id = $s1['id'];
        }

        $subcategories1 = [
            ['service_id' => $service1Id, 'name' => 'Informasi Perkara', 'slug' => 'informasi-perkara', 'description' => 'Informasi perkembangan perkara dan administrasi peradilan.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi Administrasi Perkara', 'slug' => 'informasi-administrasi-perkara', 'description' => 'Informasi kelengkapan berkas dan administrasi putusan.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi Prosedur Pelayanan', 'slug' => 'informasi-prosedur-pelayanan', 'description' => 'Panduan alur dan tahapan pelayanan Pengadilan Agama Penajam.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi Persyaratan Perkara', 'slug' => 'informasi-persyaratan-perkara', 'description' => 'Syarat-syarat pengajuan gugatan/permohonan.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi Layanan Pengadilan Agama Penajam', 'slug' => 'informasi-layanan-pa-penajam', 'description' => 'Seputar produk pengadilan, akta cerai, salinan putusan, dll.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi Jadwal Persidangan', 'slug' => 'informasi-jadwal-persidangan', 'description' => 'Cek jadwal dan ruang sidang perkara Anda.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi e-Court dan Layanan Digital', 'slug' => 'informasi-ecourt-digital', 'description' => 'Panduan registrasi e-Court, e-Filing, dan e-Payment.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi Posbakum', 'slug' => 'informasi-posbakum', 'description' => 'Bantuan hukum gratis bagi masyarakat tidak mampu.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service1Id, 'name' => 'Informasi Lainnya', 'slug' => 'informasi-lainnya', 'description' => 'Permintaan informasi publik lainnya.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($subcategories1 as $sub) {
            $exists = $db->table('service_subcategories')
                ->where('service_id', $sub['service_id'])
                ->where('slug', $sub['slug'])
                ->countAllResults() > 0;
            if (!$exists) {
                $db->table('service_subcategories')->insert($sub);
            }
        }

        $service2 = [
            'name'        => 'Layanan Pendaftaran',
            'slug'        => 'layanan-pendaftaran',
            'description' => 'Bantuan pendaftaran perkara online, verifikasi dokumen awal, dan konsultasi e-Court.',
            'icon'        => 'bi-file-earmark-text-fill',
            'status'      => 'active',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        
        $s2 = $db->table('services')->where('slug', 'layanan-pendaftaran')->get()->getRowArray();
        if (!$s2) {
            $db->table('services')->insert($service2);
            $service2Id = $db->insertID();
        } else {
            $service2Id = $s2['id'];
        }

        $subcategories2 = [
            ['service_id' => $service2Id, 'name' => 'Pendaftaran Perkara', 'slug' => 'pendaftaran-perkara', 'description' => 'Bimbingan pengajuan perkara baru secara terpandu.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service2Id, 'name' => 'Informasi Persyaratan Pendaftaran', 'slug' => 'informasi-persyaratan-pendaftaran', 'description' => 'Checklist syarat dokumen sebelum pendaftaran.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service2Id, 'name' => 'Konsultasi Proses Pendaftaran', 'slug' => 'konsultasi-proses-pendaftaran', 'description' => 'Tanya jawab seputar tahapan administrasi pendaftaran.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service2Id, 'name' => 'Verifikasi Dokumen Awal', 'slug' => 'verifikasi-dokumen-awal', 'description' => 'Pemeriksaan berkas administrasi sebelum sidang/pendaftaran resmi.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service2Id, 'name' => 'Bantuan Administrasi Pendaftaran', 'slug' => 'bantuan-administrasi-pendaftaran', 'description' => 'Panduan pengisian formulir gugatan/permohonan.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service2Id, 'name' => 'Konsultasi Pendaftaran Perkara secara Elektronik', 'slug' => 'konsultasi-pendaftaran-elektronik', 'description' => 'Bantuan teknis pendaftaran online mandiri.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['service_id' => $service2Id, 'name' => 'Bantuan Layanan e-Court', 'slug' => 'bantuan-layanan-ecourt', 'description' => 'Asistensi penggunaan aplikasi e-Court Mahkamah Agung RI.', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($subcategories2 as $sub) {
            $exists = $db->table('service_subcategories')
                ->where('service_id', $sub['service_id'])
                ->where('slug', $sub['slug'])
                ->countAllResults() > 0;
            if (!$exists) {
                $db->table('service_subcategories')->insert($sub);
            }
        }

        // 3. Seed System Settings
        $settings = [
            ['setting_key' => 'app_name', 'setting_value' => 'SI VERONIKA', 'is_encrypted' => 0],
            ['setting_key' => 'app_long_name', 'setting_value' => 'Sistem Verifikasi Online CEKAdministrasi', 'is_encrypted' => 0],
            ['setting_key' => 'institution_name', 'setting_value' => 'Pengadilan Agama Penajam', 'is_encrypted' => 0],
            ['setting_key' => 'institution_address', 'setting_value' => 'Jl. Provinsi Km. 09, Nipah-Nipah, Kec. Penajam, Kab. Penajam Paser Utara, Kalimantan Timur 76141', 'is_encrypted' => 0],
            ['setting_key' => 'institution_phone', 'setting_value' => '(0542) 7212345 / 0811-5420-123', 'is_encrypted' => 0],
            ['setting_key' => 'institution_email', 'setting_value' => 'pa.penajam@gmail.com', 'is_encrypted' => 0],
            ['setting_key' => 'institution_website', 'setting_value' => 'https://pa-penajam.go.id', 'is_encrypted' => 0],
            ['setting_key' => 'service_hours', 'setting_value' => 'Senin - Kamis: 08.00 - 15.30 WITA | Jumat: 08.00 - 16.00 WITA', 'is_encrypted' => 0],
            ['setting_key' => 'timezone_name', 'setting_value' => 'Asia/Makassar', 'is_encrypted' => 0],
            ['setting_key' => 'timezone_label', 'setting_value' => 'WITA', 'is_encrypted' => 0],
            ['setting_key' => 'zoom_reminder_offset_minutes', 'setting_value' => '10', 'is_encrypted' => 0],
            ['setting_key' => 'h1_reminder_time', 'setting_value' => '08:00', 'is_encrypted' => 0],
            ['setting_key' => 'h1h_reminder_offset_minutes', 'setting_value' => '60', 'is_encrypted' => 0],
            ['setting_key' => 'wa_provider', 'setting_value' => 'waha', 'is_encrypted' => 0],
            ['setting_key' => 'wa_api_url', 'setting_value' => 'http://36.95.108.50:3000', 'is_encrypted' => 0],
            ['setting_key' => 'wa_api_key', 'setting_value' => 'secret123', 'is_encrypted' => 0],
            ['setting_key' => 'wa_session_name', 'setting_value' => 'test', 'is_encrypted' => 0],
            ['setting_key' => 'wa_sender_number', 'setting_value' => '6285389705146', 'is_encrypted' => 0],
            ['setting_key' => 'wa_timeout', 'setting_value' => '30', 'is_encrypted' => 0],
            ['setting_key' => 'wa_max_retry', 'setting_value' => '3', 'is_encrypted' => 0],
            ['setting_key' => 'max_file_size_kb', 'setting_value' => '5120', 'is_encrypted' => 0],
            ['setting_key' => 'allowed_file_types', 'setting_value' => 'pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'is_encrypted' => 0],
            ['setting_key' => 'wa_template_registration', 'setting_value' => "Pendaftaran SI VERONIKA Pengadilan Agama Penajam berhasil.\n\nNomor Registrasi: {{nomor_registrasi}}\nNama: {{nama}}\nLayanan: {{layanan}}\nTanggal: {{tanggal}}\nWaktu: {{waktu}} WITA\n\nStatus permohonan Anda: Menunggu Verifikasi.\n\nMohon menunggu informasi selanjutnya.\n\nPengadilan Agama Penajam.", 'is_encrypted' => 0],
            ['setting_key' => 'wa_template_status_update', 'setting_value' => "Pemberitahuan Status SI VERONIKA Pengadilan Agama Penajam:\n\nNomor Registrasi: {{nomor_registrasi}}\nNama: {{nama}}\nStatus: {{status}}\nCatatan Petugas: {{catatan}}\n\nSilakan cek status Anda melalui tautan:\n{{url_cek_status}}\n\nPengadilan Agama Penajam.", 'is_encrypted' => 0],
            ['setting_key' => 'wa_template_zoom_link', 'setting_value' => "SI VERONIKA Pengadilan Agama Penajam\n\nLayanan Anda akan segera dimulai.\n\nNomor Registrasi: {{nomor_registrasi}}\nLayanan: {{layanan}}\nTanggal: {{tanggal}}\nWaktu: {{waktu}} WITA\n\nSilakan bergabung melalui Zoom:\n{{link_zoom}}\nMeeting ID: {{zoom_meeting_id}}\nPasscode: {{zoom_password}}\n\nMohon bergabung beberapa menit sebelum jadwal pelayanan.\n\nPengadilan Agama Penajam.", 'is_encrypted' => 0],
            ['setting_key' => 'wa_template_reminder_h1', 'setting_value' => "Pengingat dari SI VERONIKA Pengadilan Agama Penajam:\n\nHalo Bapak/Ibu {{nama}}, Anda memiliki jadwal layanan konsultasi online besok pada {{tanggal}} pukul {{waktu}} WITA.\nLayanan: {{layanan}}\nNomor Registrasi: {{nomor_registrasi}}\n\nMohon persiapkan dokumen terkait.\n\nPengadilan Agama Penajam.", 'is_encrypted' => 0],
            ['setting_key' => 'wa_template_reminder_h1h', 'setting_value' => "Pengingat dari SI VERONIKA Pengadilan Agama Penajam:\n\nHalo Bapak/Ibu {{nama}}, jadwal layanan Anda akan dimulai 1 jam lagi (pukul {{waktu}} WITA).\nNomor Registrasi: {{nomor_registrasi}}\n\nLink Zoom akan kami kirimkan sesaat menjelang jadwal.\n\nPengadilan Agama Penajam.", 'is_encrypted' => 0],
        ];

        foreach ($settings as $setting) {
            $exists = $db->table('system_settings')->where('setting_key', $setting['setting_key'])->countAllResults() > 0;
            if (!$exists) {
                $setting['created_at'] = date('Y-m-d H:i:s');
                $setting['updated_at'] = date('Y-m-d H:i:s');
                $db->table('system_settings')->insert($setting);
            }
        }

        // 4. Seed Holidays for current year
        $holidays = [
            ['holiday_date' => date('Y') . '-01-01', 'name' => 'Tahun Baru Masehi', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['holiday_date' => date('Y') . '-08-17', 'name' => 'Hari Kemerdekaan RI', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['holiday_date' => date('Y') . '-12-25', 'name' => 'Hari Raya Natal', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        foreach ($holidays as $h) {
            $exists = $db->table('holidays')->where('holiday_date', $h['holiday_date'])->countAllResults() > 0;
            if (!$exists) {
                $db->table('holidays')->insert($h);
            }
        }

        // 5. Seed Schedules if table is empty
        $existingSchedulesCount = $db->table('schedules')->countAllResults();
        if ($existingSchedulesCount === 0) {
            $timeSlots = [
                ['08:00:00', '08:30:00'],
                ['08:30:00', '09:00:00'],
                ['09:00:00', '09:30:00'],
                ['09:30:00', '10:00:00'],
                ['10:00:00', '10:30:00'],
                ['10:30:00', '11:00:00'],
                ['13:30:00', '14:00:00'],
                ['14:00:00', '14:30:00'],
                ['14:30:00', '15:00:00'],
            ];

            $scheduleBatch = [];
            $today = new \DateTime();
            for ($i = 0; $i < 30; $i++) {
                $currDate = clone $today;
                $currDate->modify("+$i days");
                $dayOfWeek = (int)$currDate->format('N'); // 1 = Mon, 7 = Sun
                if ($dayOfWeek >= 6) {
                    continue; // Skip weekends
                }

                $dateStr = $currDate->format('Y-m-d');
                foreach ($timeSlots as $slot) {
                    // Skip afternoon on Friday
                    if ($dayOfWeek === 5 && $slot[0] >= '13:00:00') {
                        continue;
                    }

                    $scheduleBatch[] = [
                        'date'                => $dateStr,
                        'start_time'          => $slot[0],
                        'end_time'            => $slot[1],
                        'capacity'            => 1,
                        'booked'              => 0,
                        'assigned_officer_id' => 3, // Ahmad Fauzi
                        'status'              => 'active',
                        'notes'               => 'Slot Layanan Rutin PTSP Online',
                        'created_at'          => date('Y-m-d H:i:s'),
                        'updated_at'          => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if (!empty($scheduleBatch)) {
                $db->table('schedules')->insertBatch($scheduleBatch);
            }
        }
    }
}
