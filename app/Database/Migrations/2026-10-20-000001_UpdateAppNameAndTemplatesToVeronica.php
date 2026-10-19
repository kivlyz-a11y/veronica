<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAppNameAndTemplatesToVeronica extends Migration
{
    public function up()
    {
        // 1. Perbarui nama aplikasi menjadi SI VERONICA pada tabel system_settings
        $this->db->table('system_settings')
            ->where('setting_key', 'app_name')
            ->update(['setting_value' => 'SI VERONICA', 'updated_at' => date('Y-m-d H:i:s')]);

        // 2. Perbarui seluruh template pesan WhatsApp yang mengandung SI VERONIKA
        $this->db->query("
            UPDATE system_settings 
            SET setting_value = REPLACE(setting_value, 'SI VERONIKA', 'SI VERONICA'),
                updated_at = NOW()
            WHERE setting_value LIKE '%SI VERONIKA%'
        ");

        // 3. Perbarui riwayat notifikasi pesan sebelumnya jika ada
        if ($this->db->tableExists('notifications')) {
            $this->db->query("
                UPDATE notifications 
                SET message = REPLACE(message, 'SI VERONIKA', 'SI VERONICA'),
                    updated_at = NOW()
                WHERE message LIKE '%SI VERONIKA%'
            ");
        }
    }

    public function down()
    {
        // Rollback ke SI VERONIKA jika diperlukan
        $this->db->table('system_settings')
            ->where('setting_key', 'app_name')
            ->update(['setting_value' => 'SI VERONIKA', 'updated_at' => date('Y-m-d H:i:s')]);

        $this->db->query("
            UPDATE system_settings 
            SET setting_value = REPLACE(setting_value, 'SI VERONICA', 'SI VERONIKA'),
                updated_at = NOW()
            WHERE setting_value LIKE '%SI VERONICA%'
        ");

        if ($this->db->tableExists('notifications')) {
            $this->db->query("
                UPDATE notifications 
                SET message = REPLACE(message, 'SI VERONICA', 'SI VERONIKA'),
                    updated_at = NOW()
                WHERE message LIKE '%SI VERONICA%'
            ");
        }
    }
}
