<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiVeronikaTables extends Migration
{
    public function up()
    {
        // 1. users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'unique'     => true,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['superadmin', 'admin', 'officer', 'pimpinan'],
                'default'    => 'officer',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // 2. applicants
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'institution' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'applicant_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Pemohon',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('phone');
        $this->forge->addKey('nik');
        $this->forge->createTable('applicants', true);

        // 3. services
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'unique'     => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'bi-info-circle',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('services', true);

        // 4. service_subcategories
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'service_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('service_id');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('service_subcategories', true);

        // 5. schedules
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'start_time' => [
                'type' => 'TIME',
            ],
            'end_time' => [
                'type' => 'TIME',
            ],
            'capacity' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 1,
            ],
            'booked' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'assigned_officer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'closed'],
                'default'    => 'active',
            ],
            'notes' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('date');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('assigned_officer_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('schedules', true);

        // 6. applications
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'registration_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'booking_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
            ],
            'applicant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'service_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sub_service_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'case_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'Menunggu Verifikasi',
                    'Sedang Diverifikasi',
                    'Disetujui',
                    'Perlu Perbaikan',
                    'Ditolak',
                    'Terjadwal',
                    'Sedang Berlangsung',
                    'Selesai',
                    'Dibatalkan',
                    'Tidak Hadir'
                ],
                'default'    => 'Menunggu Verifikasi',
            ],
            'verification_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('applicant_id', 'applicants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('service_id', 'services', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sub_service_id', 'service_subcategories', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('applications', true);

        // 7. appointments
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'unique'     => true,
            ],
            'schedule_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'zoom_url' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'zoom_meeting_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'zoom_password' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'zoom_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'zoom_added_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'zoom_added_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'check_in_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'check_in_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'service_started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'service_ended_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'booked',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('schedule_id');
        $this->forge->addForeignKey('application_id', 'applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('schedule_id', 'schedules', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('zoom_added_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('check_in_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('appointments', true);

        // 8. documents
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'stored_filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'filepath' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'size' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Size in bytes',
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('application_id');
        $this->forge->addForeignKey('application_id', 'applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('documents', true);

        // 9. notifications
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'wa_confirmation, wa_status, wa_zoom_link, wa_reminder_h1, wa_reminder_h1h',
            ],
            'event_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Idempotency key',
            ],
            'recipient' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'sent', 'failed'],
                'default'    => 'pending',
            ],
            'attempts' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('event_key');
        $this->forge->addKey('status');
        $this->forge->addKey('recipient');
        $this->forge->addForeignKey('application_id', 'applications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifications', true);

        // 10. audit_logs
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'reference_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('action');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('audit_logs', true);

        // 11. holidays
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'holiday_date' => [
                'type'   => 'DATE',
                'unique' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('holidays', true);

        // 12. system_settings
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'setting_value' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'is_encrypted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('system_settings', true);

        // 13. internal_notifications
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Null means for all officers/admins',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_read' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('is_read');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('internal_notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('internal_notifications', true);
        $this->forge->dropTable('system_settings', true);
        $this->forge->dropTable('holidays', true);
        $this->forge->dropTable('audit_logs', true);
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('documents', true);
        $this->forge->dropTable('appointments', true);
        $this->forge->dropTable('applications', true);
        $this->forge->dropTable('schedules', true);
        $this->forge->dropTable('service_subcategories', true);
        $this->forge->dropTable('services', true);
        $this->forge->dropTable('applicants', true);
        $this->forge->dropTable('users', true);
    }
}
