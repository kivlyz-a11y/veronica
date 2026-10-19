<?php

namespace App\Commands;

class LegacyVeronikaCleanupFiles extends CleanupFiles
{
    protected $group       = 'Veronica (Legacy)';
    protected $name        = 'veronika:cleanup-files';
    protected $description = 'Alias legacy untuk veronica:cleanup-files';
}
