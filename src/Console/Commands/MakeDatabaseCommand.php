<?php

namespace Zaber04\LumenApiResources\Console\Commands;

use Illuminate\Console\Command;
use PDO;

class MakeDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:database {dbname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database (if not exists)';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            $dbname = $this->argument('dbname');

            // using raw PHP --> We can accept a connection string as argument to make versatile (have to change signature with extra optional argument)
            $default_connection = "mysql:host=" . env('DB_HOST') . ";port=" . env('DB_PORT');
            $user = env('DB_USERNAME');
            $pass = env('DB_PASSWORD');

            $connection = new PDO($default_connection, $user, $pass);
            // we should use a check exist method to make versatile in production scenario
            $connection->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
