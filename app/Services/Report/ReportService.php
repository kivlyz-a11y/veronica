<?php

namespace App\Services\Report;

use App\Models\ApplicationModel;
use App\Models\SystemSettingModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    protected ApplicationModel $applicationModel;
    protected SystemSettingModel $settingModel;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->settingModel     = new SystemSettingModel();
    }

    /**
     * Get report data based on filters
     */
    public function getReportData(array $filters = []): array
    {
        $builder = $this->applicationModel->builder();
        $builder->select('
            applications.*,
            applicants.name as applicant_name,
            applicants.nik as applicant_nik,
            applicants.phone as applicant_phone,
            applicants.applicant_role,
            services.name as service_name,
            service_subcategories.name as sub_service_name,
            schedules.date as schedule_date,
            schedules.start_time as schedule_start_time,
            schedules.end_time as schedule_end_time,
            verifier.name as verifier_name
        ')
        ->join('applicants', 'applicants.id = applications.applicant_id', 'left')
        ->join('services', 'services.id = applications.service_id', 'left')
        ->join('service_subcategories', 'service_subcategories.id = applications.sub_service_id', 'left')
        ->join('appointments', 'appointments.application_id = applications.id', 'left')
        ->join('schedules', 'schedules.id = appointments.schedule_id', 'left')
        ->join('users as verifier', 'verifier.id = applications.verified_by', 'left');

        if (!empty($filters['date_from'])) {
            $builder->where('schedules.date >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('schedules.date <=', $filters['date_to']);
        }
        if (!empty($filters['service_id'])) {
            $builder->where('applications.service_id', $filters['service_id']);
        }
        if (!empty($filters['status'])) {
            $builder->where('applications.status', $filters['status']);
        }

        $builder->orderBy('schedules.date', 'DESC');
        $builder->orderBy('schedules.start_time', 'ASC');

        $rows = $builder->get()->getResultArray();

        // Calculate statistics summary
        $stats = [
            'total'               => count($rows),
            'layanan_informasi'   => 0,
            'layanan_pendaftaran' => 0,
            'selesai'             => 0,
            'dibatalkan'          => 0,
            'ditolak'             => 0,
            'tidak_hadir'         => 0,
            'disetujui'           => 0,
            'menunggu'            => 0,
        ];

        foreach ($rows as $r) {
            if (stripos($r['service_name'] ?? '', 'informasi') !== false) {
                $stats['layanan_informasi']++;
            } else {
                $stats['layanan_pendaftaran']++;
            }

            switch ($r['status']) {
                case 'Selesai':
                    $stats['selesai']++;
                    break;
                case 'Dibatalkan':
                    $stats['dibatalkan']++;
                    break;
                case 'Ditolak':
                    $stats['ditolak']++;
                    break;
                case 'Tidak Hadir':
                    $stats['tidak_hadir']++;
                    break;
                case 'Disetujui':
                case 'Terjadwal':
                    $stats['disetujui']++;
                    break;
                case 'Menunggu Verifikasi':
                case 'Sedang Diverifikasi':
                    $stats['menunggu']++;
                    break;
            }
        }

        return [
            'rows'    => $rows,
            'stats'   => $stats,
            'filters' => $filters,
        ];
    }

    /**
     * Generate Excel spreadsheet using PhpSpreadsheet
     */
    public function generateExcel(array $reportData): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pelayanan');

        // Header Title
        $sheet->setCellValue('A1', 'PENGADILAN AGAMA PENAJAM');
        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI PELAYANAN ONLINE SI VERONIKA');
        $dateInfo = 'Periode: ' . (!empty($reportData['filters']['date_from']) ? $reportData['filters']['date_from'] : 'Semua') . ' s/d ' . (!empty($reportData['filters']['date_to']) ? $reportData['filters']['date_to'] : 'Semua');
        $sheet->setCellValue('A3', $dateInfo);

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Header
        $headers = ['No', 'No. Registrasi', 'Nama Pemohon', 'NIK', 'No. WhatsApp', 'Jenis Layanan', 'Jadwal Layanan', 'Status', 'Petugas Verifikator'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        $rowNum = 5;
        foreach ($headers as $idx => $header) {
            $sheet->setCellValue($cols[$idx] . $rowNum, $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E7E34'], // Dark green
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray($headerStyle);
        $sheet->getRowDimension($rowNum)->setRowHeight(25);

        // Data Rows
        $rowNum = 6;
        $no = 1;
        foreach ($reportData['rows'] as $row) {
            $jadwal = ($row['schedule_date'] ?? '-') . ' ' . (substr($row['schedule_start_time'] ?? '', 0, 5));
            $sheet->setCellValue("A{$rowNum}", $no++);
            $sheet->setCellValue("B{$rowNum}", $row['registration_number']);
            $sheet->setCellValue("C{$rowNum}", $row['applicant_name']);
            $sheet->setCellValueExplicit("D{$rowNum}", (string)$row['applicant_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("E{$rowNum}", (string)$row['applicant_phone'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("F{$rowNum}", $row['service_name'] . ($row['sub_service_name'] ? " - {$row['sub_service_name']}" : ''));
            $sheet->setCellValue("G{$rowNum}", $jadwal);
            $sheet->setCellValue("H{$rowNum}", $row['status']);
            $sheet->setCellValue("I{$rowNum}", $row['verifier_name'] ?? '-');

            $rowNum++;
        }

        $lastRow = $rowNum - 1;
        if ($lastRow >= 6) {
            $dataBorderStyle = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D0D0']]],
            ];
            $sheet->getStyle("A6:I{$lastRow}")->applyFromArray($dataBorderStyle);
        }

        // Auto size columns
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempPath = WRITEPATH . 'cache/laporan_veronika_' . date('Ymd_His') . '.xlsx';
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Generate PDF using Dompdf
     */
    public function generatePdf(string $html): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
