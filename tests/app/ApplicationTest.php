<?php

namespace Tests;

use App\Database\Seeds\InitialSeeder;
use App\Models\ApplicationModel;
use App\Models\ScheduleModel;
use App\Services\Registration\RegistrationNumberService;
use App\Services\Registration\RegistrationService;
use App\Services\WhatsApp\MockWhatsAppProvider;
use App\Services\WhatsApp\WhatsAppService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class ApplicationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $basePath   = APPPATH . 'Database';
    protected $namespace  = 'App';
    protected $migrate    = true;
    protected $migrateOnce= true;
    protected $seed       = 'InitialSeeder';
    protected $seedOnce   = true;

    public function testRegistrationNumberGeneration()
    {
        $service = new RegistrationNumberService();
        $regNumber = $service->generateRegistrationNumber('2026-08-29');

        $this->assertStringStartsWith('VER-20260829-', $regNumber);
        $this->assertSame(17, strlen($regNumber));
    }

    public function testBookingCodeGeneration()
    {
        $service = new RegistrationNumberService();
        $code = $service->generateBookingCode();

        $this->assertStringStartsWith('BK-', $code);
    }

    public function testMockWhatsAppProvider()
    {
        $mock = new MockWhatsAppProvider();
        $res = $mock->sendMessage('081234567890', 'Pesan Uji Coba SI VERONICA');

        $this->assertTrue($res['success']);
    }

    public function testWhatsAppTemplateRendering()
    {
        $service = new WhatsAppService(new MockWhatsAppProvider());
        $rendered = $service->renderTemplate('Halo {{nama}}, No Reg: {{nomor_registrasi}}, Instansi: {{nama_instansi}}', [
            'nama'             => 'Ahmad',
            'nomor_registrasi' => 'VER-20260829-0001',
        ]);

        $this->assertStringContainsString('Halo Ahmad, No Reg: VER-20260829-0001', $rendered);
        $this->assertStringContainsString('Pengadilan Agama Penajam', $rendered);
    }

    public function testRegistrationSubmission()
    {
        $regService = new RegistrationService();
        $schedule = (new ScheduleModel())->where('status', 'active')->first();

        $this->assertNotNull($schedule, 'Schedule should exist from seeder');

        $applicant = [
            'name'           => 'Rina Wulandari',
            'nik'            => '6409015507950002',
            'phone'          => '081345678901',
            'email'          => 'rina.w@example.com',
            'address'        => 'Nipah-Nipah, Penajam',
            'institution'    => '',
            'applicant_role' => 'Pemohon',
        ];

        $application = [
            'service_id'     => 1,
            'sub_service_id' => 1,
            'subject'        => 'Konsultasi Perkara Gugatan',
            'description'    => 'Uji coba pendaftaran permohonan layanan online.',
            'case_number'    => '',
            'notes'          => '',
        ];

        $res = $regService->submitApplication($applicant, $application, (int)$schedule['id'], []);

        $this->assertTrue($res['success']);
        $this->assertNotEmpty($res['registration_number']);

        // Verify record in database
        $appModel = new ApplicationModel();
        $saved = $appModel->find($res['application_id']);
        $this->assertNotNull($saved);
        $this->assertSame('Menunggu Verifikasi', $saved['status']);
    }

    public function testRecurringScheduleGeneration()
    {
        $schedModel = new ScheduleModel();
        
        // Target a 7-day window
        $startDate = new \DateTime('2026-11-02'); // Monday
        $endDate   = new \DateTime('2026-11-08'); // Sunday
        $days      = [1, 2, 3, 4, 5]; // Monday to Friday
        
        $batch = [];
        $curr = clone $startDate;
        while ($curr <= $endDate) {
            $dayOfWeek = (int)$curr->format('N');
            if (in_array($dayOfWeek, $days, true)) {
                $batch[] = [
                    'date'       => $curr->format('Y-m-d'),
                    'start_time' => '08:00:00',
                    'end_time'   => '08:30:00',
                    'capacity'   => 2,
                    'booked'     => 0,
                    'status'     => 'active',
                ];
            }
            $curr->modify('+1 day');
        }

        // Exactly 5 workdays (Mon-Fri) should be generated out of 7 days
        $this->assertCount(5, $batch);
        $schedModel->insertBatch($batch);

        $slots = $schedModel->where('date >=', '2026-11-02')
                            ->where('date <=', '2026-11-08')
                            ->where('capacity', 2)
                            ->findAll();
        $this->assertCount(5, $slots);
    }
}
