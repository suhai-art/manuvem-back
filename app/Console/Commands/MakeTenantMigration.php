<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeTenantMigration extends Command
{
    protected $signature = 'make:tenant-migration {name?} {--m} {--f}';

    protected $description = 'Cria uma Migration no tenant';

    public function handle(): int
    {
        $name = $this->argument('name');

        $params = [
            '--path' => 'database/migrations/tenant',
        ];

        if ($name) {
            $params['name'] = $name;
        }

        $this->call('make:migration', $params);

        return self::SUCCESS;
    }
}
