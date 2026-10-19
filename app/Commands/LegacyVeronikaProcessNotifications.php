<?php

namespace App\Commands;

class LegacyVeronikaProcessNotifications extends ProcessNotifications
{
    protected $group       = 'Veronica (Legacy)';
    protected $name        = 'veronika:process-notifications';
    protected $description = 'Alias legacy untuk veronica:process-notifications';
}
