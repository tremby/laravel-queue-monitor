<?php
namespace Tremby\QueueMonitor\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tremby\QueueMonitor\QueueMonitor;

class QueueQueueCheckCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:queuecheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Queue a check to make sure a queue is functioning properly";

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
     * Execute the console command for Laravel 5.4 or older
     *
     * @return mixed
     */
    public function fire()
    {
        QueueMonitor::queueQueueCheck($this->argument('queue'));
    }

    /**
     * Execute the console command for Laravel 5.5+
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fire();
    }

    /**
     * Execute the console command for Laravel 5.5+
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fire();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'queue',
                InputArgument::OPTIONAL,
                "Queue to queue a check for"
                . " (default is the application's default queue)",
                null,
            ],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
