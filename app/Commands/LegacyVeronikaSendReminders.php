<?php

namespace App\Commands;

class LegacyVeronikaSendReminders extends SendReminders
{
    protected $group       = 'Veronica (Legacy)';
    protected $name        = 'veronika:send-reminders';
    protected $description = 'Alias legacy untuk veronica:send-reminders';
}
