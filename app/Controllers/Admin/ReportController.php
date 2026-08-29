<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ServiceModel;
use App\Services\Report\ReportService;

class ReportController extends BaseController
{
    protected ReportService $reportService;
    protected ServiceModel $serviceModel;

    public function __construct()
    {
        $this->reportService = new ReportService();
        $this->serviceModel  = new ServiceModel();
    }

    public function index()
    {
        helper(['form', 'veronika']);

        $filters = [
            'date_from'  => $this->request->getGet('date_from'),
            'date_to'    => $this->request->getGet('date_to'),
            'service_id' => $this->request->getGet('service_id'),
            'status'     => $this->request->getGet('status'),
        ];

        $reportData = $this->reportService->getReportData($filters);
        $services   = $this->serviceModel->findAll();

        $data = [
            'title'      => 'Laporan & Rekapitulasi Pelayanan - SI VERONIKA',
            'reportData' => $reportData,
            'services'   => $services,
            'filters'    => $filters,
        ];

        return view('admin/reports/index', $data);
    }

    public function exportExcel()
    {
        $filters = [
            'date_from'  => $this->request->getGet('date_from'),
            'date_to'    => $this->request->getGet('date_to'),
            'service_id' => $this->request->getGet('service_id'),
            'status'     => $this->request->getGet('status'),
        ];

        $reportData = $this->reportService->getReportData($filters);
        $filePath   = $this->reportService->generateExcel($reportData);

        return $this->response->download($filePath, null)->setFileName('Laporan_SI_VERONIKA_' . date('Ymd_His') . '.xlsx');
    }

    public function exportPdf()
    {
        helper('veronika');

        $filters = [
            'date_from'  => $this->request->getGet('date_from'),
            'date_to'    => $this->request->getGet('date_to'),
            'service_id' => $this->request->getGet('service_id'),
            'status'     => $this->request->getGet('status'),
        ];

        $reportData = $this->reportService->getReportData($filters);
        $html = view('admin/reports/pdf', ['reportData' => $reportData]);

        $pdfContent = $this->reportService->generatePdf($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="Laporan_SI_VERONIKA_' . date('Ymd_His') . '.pdf"')
            ->setBody($pdfContent);
    }
}
