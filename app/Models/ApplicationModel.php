<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table            = 'applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'registration_number',
        'booking_code',
        'applicant_id',
        'service_id',
        'sub_service_id',
        'subject',
        'description',
        'case_number',
        'notes',
        'status',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    protected $useTimestamps = true;

    /**
     * Get detailed application record with applicant, service, appointment, schedule, and officer
     */
    public function getDetailed(int $id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            applications.*,
            applicants.name as applicant_name,
            applicants.nik as applicant_nik,
            applicants.phone as applicant_phone,
            applicants.email as applicant_email,
            applicants.address as applicant_address,
            applicants.institution as applicant_institution,
            applicants.applicant_role,
            services.name as service_name,
            services.icon as service_icon,
            service_subcategories.name as sub_service_name,
            schedules.date as schedule_date,
            schedules.start_time as schedule_start_time,
            schedules.end_time as schedule_end_time,
            appointments.id as appointment_id,
            appointments.zoom_url,
            appointments.zoom_meeting_id,
            appointments.zoom_password,
            appointments.zoom_notes,
            appointments.zoom_added_at,
            appointments.check_in_at,
            appointments.service_started_at,
            appointments.service_ended_at,
            appointments.status as appointment_status,
            verifier.name as verifier_name,
            zoom_officer.name as zoom_officer_name,
            checkin_officer.name as checkin_officer_name
        ');
        $builder->join('applicants', 'applicants.id = applications.applicant_id', 'left');
        $builder->join('services', 'services.id = applications.service_id', 'left');
        $builder->join('service_subcategories', 'service_subcategories.id = applications.sub_service_id', 'left');
        $builder->join('appointments', 'appointments.application_id = applications.id', 'left');
        $builder->join('schedules', 'schedules.id = appointments.schedule_id', 'left');
        $builder->join('users as verifier', 'verifier.id = applications.verified_by', 'left');
        $builder->join('users as zoom_officer', 'zoom_officer.id = appointments.zoom_added_by', 'left');
        $builder->join('users as checkin_officer', 'checkin_officer.id = appointments.check_in_by', 'left');
        $builder->where('applications.id', $id);

        return $builder->get()->getRowArray();
    }

    /**
     * Find application by registration number and phone (for public check status)
     */
    public function findByRegistrationAndPhone(string $regNum, string $phone)
    {
        // Normalize phone number (handle 08..., 628..., +628...)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '62')) {
            $altPhone = '0' . substr($cleanPhone, 2);
        } elseif (str_starts_with($cleanPhone, '0')) {
            $altPhone = '62' . substr($cleanPhone, 1);
        } else {
            $altPhone = $cleanPhone;
        }

        $builder = $this->db->table($this->table);
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
            appointments.zoom_url,
            appointments.zoom_meeting_id,
            appointments.zoom_password,
            appointments.zoom_notes,
            appointments.check_in_at,
            appointments.service_started_at,
            appointments.service_ended_at
        ');
        $builder->join('applicants', 'applicants.id = applications.applicant_id', 'left');
        $builder->join('services', 'services.id = applications.service_id', 'left');
        $builder->join('service_subcategories', 'service_subcategories.id = applications.sub_service_id', 'left');
        $builder->join('appointments', 'appointments.application_id = applications.id', 'left');
        $builder->join('schedules', 'schedules.id = appointments.schedule_id', 'left');
        $builder->where('applications.registration_number', trim($regNum));
        $builder->groupStart()
                ->where('applicants.phone', $phone)
                ->orWhere('applicants.phone', $cleanPhone)
                ->orWhere('applicants.phone', $altPhone)
                ->groupEnd();

        return $builder->get()->getRowArray();
    }

    /**
     * Get applications with filters & pagination
     */
    public function getFiltered(array $filters = [], int $perPage = 15)
    {
        $builder = $this->select('
            applications.*,
            applicants.name as applicant_name,
            applicants.phone as applicant_phone,
            applicants.nik as applicant_nik,
            services.name as service_name,
            schedules.date as schedule_date,
            schedules.start_time as schedule_start_time,
            schedules.end_time as schedule_end_time,
            appointments.zoom_url,
            appointments.check_in_at
        ')
        ->join('applicants', 'applicants.id = applications.applicant_id', 'left')
        ->join('services', 'services.id = applications.service_id', 'left')
        ->join('appointments', 'appointments.application_id = applications.id', 'left')
        ->join('schedules', 'schedules.id = appointments.schedule_id', 'left');

        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('applications.registration_number', $filters['search'])
                    ->orLike('applicants.name', $filters['search'])
                    ->orLike('applicants.phone', $filters['search'])
                    ->orLike('applications.subject', $filters['search'])
                    ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('applications.status', $filters['status']);
        }

        if (!empty($filters['service_id'])) {
            $builder->where('applications.service_id', $filters['service_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('schedules.date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('schedules.date <=', $filters['date_to']);
        }

        $builder->orderBy('applications.id', 'DESC');

        return [
            'data'  => $this->paginate($perPage),
            'pager' => $this->pager,
        ];
    }
}
