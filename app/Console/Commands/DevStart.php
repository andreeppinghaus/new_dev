<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;

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
        exec('cd '.base_path().'\Modules\Base && git checkout develop', $output);
        exec('cd '.base_path().'.\Modules\Contact && git checkout develop', $output);
        exec('git clone https://github.com/DaviMenezes/exemplo-module.git Modules\Exemplo', $output);
        exec('cd '.base_path().'.\Modules\Exemplo && git checkout develop', $output);

        $output = [];

        $this->warn('key:generate');
        $this->call('key:generate');

        $this->warn('migrate --step');
        $this->call('migrate:fresh', ['--step']);

        $modules = Module::allEnabled();

        foreach ($modules as $module) {
            $this->warn('seeding ' . $module->getLowerName() .   ' module');
            $this->call('db:seed', ['--class' => 'Modules\\' . $module->getName() . '\\Database\\Seeders\\' . $module->getName() . 'DatabaseSeeder']);
        }

        $this->warn('contact:new.tenant.user');
        $this->call('contact:new.tenant.user');

        $this->warn('passport:keys');
        $this->call('passport:keys');

        $this->warn('passport:client --password');
        $this->call('passport:client', ['--password' => true]);
    }
}
