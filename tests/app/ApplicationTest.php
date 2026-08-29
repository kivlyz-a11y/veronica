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
        $res = $mock->sendMessage('081234567890', 'Pesan Uji Coba SI VERONIKA');

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
}
