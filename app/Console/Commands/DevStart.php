<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DevStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $output = [];
        $this->warn('git checkout develop');
        exec('git checkout develop', $output);
        collect($output)->each(fn($i) => $this->info($i));

        $output = [];
        $this->warn('creating .env');
        exec('cp .env.example .env', $output);
        collect($output)->each(fn($i) => $this->info($i));

        $output = [];
        exec('composer install', $output);
        collect($output)->each(fn($i) => $this->info($i));

        $this->warn('key:generate');
        $this->call('key:generate');

        $this->warn('migrate --step');
        $this->call('migrate:fresh', ['--step']);

        $this->warn('seeding base module');
        $this->call('db:seed', ['--class' => 'Modules\Base\Database\Seeders\BaseDatabaseSeeder']);

        $this->warn('seeding contact module');
        $this->call('db:seed', ['--class' => 'Modules\Contact\Database\Seeders\ContactDatabaseSeeder']);

        $this->warn('contact:new.tenant.user');
        $this->call('contact:new.tenant.user');

        $this->warn('passport:keys');
        $this->call('passport:keys');

        $this->warn('passport:client --password');
        $this->call('passport:client', ['--password' => true]);
    }
}
