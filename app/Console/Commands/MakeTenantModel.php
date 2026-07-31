<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeTenantModel extends Command
{
    protected $signature = 'make:tenant-model {name} {--m} {--f}';

    protected $description = 'Cria um Model para tenant e sua migration';

    public function handle(): int
    {
        $name = $this->argument('name');

        $this->call('make:model', [
            'name' => $name,
        ]);

        if ($this->option('m')) {
            $table = str($name)->snake()->plural();

            $this->call('make:migration', [
                'name' => 'create_' . $table . '_table',
                '--path' => 'database/migrations/tenant',
            ]);
        }

        if ($this->option('f')) {
            $this->call('make:factory', [
                'name' => $name
            ]);
        }

        $this->info("Tenant model {$name} criado com sucesso!");

        return self::SUCCESS;
    }
}
