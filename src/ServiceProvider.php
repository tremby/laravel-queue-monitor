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
            $view->withQueues(array_map(function ($queueName) {
                $status = QueueStatus::get($queueName);
                if (!$status) {
                    $status = new QueueStatus($queueName, QueueStatus::ERROR, false);
                    $status->setMessage("Status not found in cache; is a cron job set up and running?");
                }
                return $status;
            }, Cache::get(QueueMonitor::QUEUES_CACHE_KEY, [])));
        };
        View::composer('queue-monitor::status', $composer);
        View::composer('queue-monitor::status-json', $composer);
    }
}
