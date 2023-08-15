<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Clean extends Command
{
    protected $signature = 'custom:clean';

    protected $description = 'Clear cache and config files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('config:cache');

        $this->info('Cache and config files cleared successfully!');
    }
}
