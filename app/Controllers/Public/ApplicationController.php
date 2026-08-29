<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\ApplicationModel;
use App\Models\DocumentModel;
use App\Models\ServiceModel;
use App\Models\ServiceSubcategoryModel;
use App\Services\Registration\RegistrationService;
use App\Services\Report\ReportService;

class ApplicationController extends BaseController
{
    protected ServiceModel $serviceModel;
    protected ServiceSubcategoryModel $subcatModel;
    protected ApplicationModel $applicationModel;
    protected RegistrationService $registrationService;

    public function __construct()
    {
        $this->serviceModel        = new ServiceModel();
        $this->subcatModel         = new ServiceSubcategoryModel();
        $this->applicationModel    = new ApplicationModel();
        $this->registrationService = new RegistrationService();
    }

    /**
     * Display registration form
     */
    public function create()
    {
        helper(['form', 'veronika']);

        $selectedServiceSlug = $this->request->getGet('layanan');
        $services = $this->serviceModel->getActiveServices();

        $selectedService = null;
        if ($selectedServiceSlug) {
            foreach ($services as $srv) {
                if ($srv['slug'] === $selectedServiceSlug) {
                    $selectedService = $srv;
                    break;
                }
            }
        }

        $data = [
            'title'           => 'Ajukan Permintaan Layanan - SI VERONIKA',
            'services'        => $services,
            'selectedService' => $selectedService,
        ];

        return view('public/apply', $data);
    }

    /**
     * Process application submission
     */
    public function store()
    {
        helper(['form', 'veronika']);

        $rules = [
            'name'           => 'required|min_length[3]|max_length[150]',
            'nik'            => 'required|exact_length[16]|numeric',
            'phone'          => 'required|min_length[9]|max_length[20]',
            'email'          => 'permit_empty|valid_email|max_length[150]',
            'address'        => 'permit_empty|max_length[500]',
            'institution'    => 'permit_empty|max_length[150]',
            'applicant_role' => 'required',
            'service_id'     => 'required|is_natural_no_zero',
            'sub_service_id' => 'permit_empty|is_natural_no_zero',
            'subject'        => 'required|min_length[5]|max_length[255]',
            'description'    => 'required|min_length[10]',
            'schedule_id'    => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // File validation
        $files = $this->request->getFileMultiple('documents');
        $validFiles = [];
        if ($files) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $mime = $file->getClientMimeType();
                    $size = $file->getSize(); // in bytes

                    if ($size > 5 * 1024 * 1024) { // 5MB
                        return redirect()->back()->withInput()->with('error', 'Ukuran dokumen melebihi batas yang diizinkan (maksimal 5MB per file).');
                    }

                    $allowedMimes = [
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];

                    if (!in_array($mime, $allowedMimes)) {
                        return redirect()->back()->withInput()->with('error', 'Format dokumen tidak didukung. Harap unggah format PDF, JPG, PNG, DOC, atau XLS.');
                    }

                    $validFiles[] = $file;
                }
            }
        }

        $applicantData = [
            'name'           => $this->request->getPost('name'),
            'nik'            => $this->request->getPost('nik'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
            'address'        => $this->request->getPost('address'),
            'institution'    => $this->request->getPost('institution'),
            'applicant_role' => $this->request->getPost('applicant_role'),
        ];

        $applicationData = [
            'service_id'     => $this->request->getPost('service_id'),
            'sub_service_id' => $this->request->getPost('sub_service_id'),
            'subject'        => $this->request->getPost('subject'),
            'description'    => $this->request->getPost('description'),
            'case_number'    => $this->request->getPost('case_number'),
            'notes'          => $this->request->getPost('notes'),
        ];

        $scheduleId = (int)$this->request->getPost('schedule_id');

        $result = $this->registrationService->submitApplication(
            $applicantData,
            $applicationData,
            $scheduleId,
            $validFiles
        );

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        // Store registration info in flashdata and redirect to success page
        session()->setFlashdata('success_registration_number', $result['registration_number']);
        return redirect()->to(site_url("pendaftaran-berhasil/{$result['registration_number']}"));
    }

    /**
     * Registration Success Page
     */
    public function success(string $registrationNumber)
    {
        helper('veronika');

        $app = $this->applicationModel->where('registration_number', $registrationNumber)->first();
        if (!$app) {
            return redirect()->to(site_url('/'))->with('error', 'Nomor registrasi tidak ditemukan.');
        }

        $detailed = $this->applicationModel->getDetailed($app['id']);

        // Generate QR code data URI for verification
        $verifyUrl = site_url("verifikasi-tiket/{$app['registration_number']}/{$app['booking_code']}");
        $qrCodeSvg = generate_qr_data_uri($verifyUrl);

        $data = [
            'title'       => 'Pendaftaran Berhasil - SI VERONIKA',
            'application' => $detailed,
            'qrCodeSvg'   => $qrCodeSvg,
            'verifyUrl'   => $verifyUrl,
        ];

        return view('public/success', $data);
    }

    /**
     * Download PDF Ticket / Bukti Pendaftaran
     */
    public function downloadPdf(string $registrationNumber)
    {
        helper('veronika');

        $app = $this->applicationModel->where('registration_number', $registrationNumber)->first();
        if (!$app) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Permohonan tidak ditemukan.');
        }

        $detailed = $this->applicationModel->getDetailed($app['id']);
        $verifyUrl = site_url("verifikasi-tiket/{$app['registration_number']}/{$app['booking_code']}");
        $qrCodeSvg = generate_qr_data_uri($verifyUrl);

        $data = [
            'application' => $detailed,
            'qrCodeSvg'   => $qrCodeSvg,
            'verifyUrl'   => $verifyUrl,
        ];

        $html = view('public/ticket_pdf', $data);

        $reportService = new ReportService();
        $pdfContent = $reportService->generatePdf($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="Bukti_Pendaftaran_' . $app['registration_number'] . '.pdf"')
            ->setBody($pdfContent);
    }
}
