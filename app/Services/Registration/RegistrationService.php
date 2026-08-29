<?php

namespace App\Services\Registration;

use App\Models\ApplicantModel;
use App\Models\ApplicationModel;
use App\Models\AppointmentModel;
use App\Models\DocumentModel;
use App\Models\InternalNotificationModel;
use App\Models\ScheduleModel;
use App\Services\Audit\AuditService;
use App\Services\Notification\NotificationService;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\Files\UploadedFile;

class RegistrationService
{
    protected BaseConnection $db;
    protected RegistrationNumberService $numService;
    protected ApplicantModel $applicantModel;
    protected ApplicationModel $applicationModel;
    protected AppointmentModel $appointmentModel;
    protected DocumentModel $documentModel;
    protected ScheduleModel $scheduleModel;
    protected NotificationService $notificationService;
    protected AuditService $auditService;
    protected InternalNotificationModel $internalNotifModel;

    public function __construct()
    {
        $this->db                  = \Config\Database::connect();
        $this->numService          = new RegistrationNumberService();
        $this->applicantModel      = new ApplicantModel();
        $this->applicationModel    = new ApplicationModel();
        $this->appointmentModel    = new AppointmentModel();
        $this->documentModel       = new DocumentModel();
        $this->scheduleModel       = new ScheduleModel();
        $this->notificationService = new NotificationService();
        $this->auditService        = new AuditService();
        $this->internalNotifModel  = new InternalNotificationModel();
    }

    /**
     * Submit an application transactionally with row locking to prevent double booking
     *
     * @param array $applicantData
     * @param array $applicationData
     * @param int $scheduleId
     * @param array<UploadedFile> $uploadedFiles
     * @return array [ 'success' => bool, 'message' => string, 'application_id' => int|null, 'registration_number' => string|null ]
     */
    public function submitApplication(
        array $applicantData,
        array $applicationData,
        int $scheduleId,
        array $uploadedFiles = []
    ): array {
        $this->db->transBegin();

        try {
            // 1. Lock and verify schedule slot
            $schedule = $this->db->table('schedules')
                ->where('id', $scheduleId)
                ->where('status', 'active')
                ->get(1, 0, false) // SELECT FOR UPDATE when transaction is active
                ->getRowArray();

            if (!$schedule) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Jadwal pelayanan tidak ditemukan atau sudah ditutup.'];
            }

            if ($schedule['booked'] >= $schedule['capacity']) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'SLOT PENUH. Slot pelayanan ini sudah terisi penuh oleh pemohon lain.'];
            }

            // Check if slot time has passed for today
            $today = date('Y-m-d');
            $now = date('H:i:s');
            if ($schedule['date'] === $today && $schedule['start_time'] <= $now) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Slot waktu yang dipilih sudah terlewat. Silakan pilih slot lain.'];
            }

            // 2. Insert or update applicant
            $applicantId = $this->applicantModel->insert([
                'name'           => trim($applicantData['name']),
                'nik'            => trim($applicantData['nik']),
                'phone'          => trim($applicantData['phone']),
                'email'          => !empty($applicantData['email']) ? trim($applicantData['email']) : null,
                'address'        => !empty($applicantData['address']) ? trim($applicantData['address']) : null,
                'institution'    => !empty($applicantData['institution']) ? trim($applicantData['institution']) : null,
                'applicant_role' => $applicantData['applicant_role'] ?? 'Pemohon',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            if (!$applicantId) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Gagal menyimpan data identitas pemohon.'];
            }

            // 3. Generate registration number & booking code
            $regNumber   = $this->numService->generateRegistrationNumber($schedule['date']);
            $bookingCode = $this->numService->generateBookingCode();

            // 4. Create application
            $appId = $this->applicationModel->insert([
                'registration_number' => $regNumber,
                'booking_code'        => $bookingCode,
                'applicant_id'        => $applicantId,
                'service_id'          => (int)$applicationData['service_id'],
                'sub_service_id'      => !empty($applicationData['sub_service_id']) ? (int)$applicationData['sub_service_id'] : null,
                'subject'             => trim($applicationData['subject']),
                'description'         => trim($applicationData['description']),
                'case_number'         => !empty($applicationData['case_number']) ? trim($applicationData['case_number']) : null,
                'notes'               => !empty($applicationData['notes']) ? trim($applicationData['notes']) : null,
                'status'              => 'Menunggu Verifikasi',
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);

            if (!$appId) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Gagal membuat nomor permohonan.'];
            }

            // 5. Create appointment
            $this->appointmentModel->insert([
                'application_id' => $appId,
                'schedule_id'    => $scheduleId,
                'status'         => 'booked',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            // 6. Increment booked count on schedule
            $this->scheduleModel->update($scheduleId, [
                'booked'     => $schedule['booked'] + 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // 7. Handle uploaded documents
            $uploadPath = WRITEPATH . 'uploads/documents/' . date('Y/m/');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            foreach ($uploadedFiles as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $originalName = $file->getClientName();
                    $storedName   = $file->getRandomName();
                    $mimeType     = $file->getClientMimeType();
                    $fileSize     = $file->getSize();

                    $file->move($uploadPath, $storedName);

                    $this->documentModel->insert([
                        'application_id'  => $appId,
                        'filename'        => $originalName,
                        'stored_filename' => $storedName,
                        'filepath'        => 'uploads/documents/' . date('Y/m/') . $storedName,
                        'mime_type'       => $mimeType,
                        'size'            => $fileSize,
                        'uploaded_at'     => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // 8. Create internal notification for officers
            $this->internalNotifModel->insert([
                'user_id'    => null, // all officers/admins
                'title'      => 'Permohonan Layanan Baru Masuk',
                'message'    => "Permohonan baru {$regNumber} dari {$applicantData['name']} memerlukan verifikasi berkas.",
                'url'        => "admin/applications/{$appId}",
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // 9. Audit log
            $this->auditService->log(
                null,
                'submit_application',
                'applications',
                $regNumber,
                "Pemohon {$applicantData['name']} mengajukan permohonan layanan online."
            );

            // Commit transaction
            $this->db->transCommit();

            // 10. Send WhatsApp Confirmation asynchronously / immediately
            try {
                $this->notificationService->sendRegistrationConfirmation($appId);
            } catch (\Exception $e) {
                log_message('error', '[RegistrationService] Failed to send WhatsApp confirmation: ' . $e->getMessage());
            }

            return [
                'success'             => true,
                'message'             => 'Permohonan berhasil didaftarkan.',
                'application_id'      => $appId,
                'registration_number' => $regNumber,
                'booking_code'        => $bookingCode,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[RegistrationService] Error submitting application: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }
}
