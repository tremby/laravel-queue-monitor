<?php
namespace Tremby\QueueMonitor;

use Cache;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use View;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->commands([
            Command\QueueQueueCheckCommand::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // Register namespaces
        $this->package('tremby/queue-monitor', null, dirname(__DIR__) . '/resources');

        // Composer for the status views
        $composer = function ($view) {
            $queues = [];
            foreach (Cache::get(QueueMonitor::QUEUES_CACHE_KEY, []) as $queueName) {
                $status = QueueStatus::get($queueName);
                if (!$status) {
                    $status = new QueueStatus($queueName, QueueStatus::ERROR, false);
                    $status->setMessage("Status not found in cache; is a cron job set up and running?");
                }
                $queues[$queueName] = $status;
            }
            $view->withQueues($queues);
        };
        View::composer('queue-monitor::status', $composer);
        View::composer('queue-monitor::status-json', $composer);
    }
}
