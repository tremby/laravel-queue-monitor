<?php
namespace Tremby\QueueMonitor;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

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
    public function boot(ViewFactory $viewFactory, CacheRepository $cache)
    {
        // Define views path
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'queue-monitor');

        // Composer for the status views
        $composer = function ($view) use ($cache) {
            $view->withQueues(array_map(function ($queueName) {
                $status = QueueStatus::get($queueName);
                if (!$status) {
                    $status = new QueueStatus($queueName, QueueStatus::ERROR, false);
                    $status->setMessage("Status not found in cache; is a cron job set up and running?");
                }
                return $status;
            }, $cache->get(QueueMonitor::QUEUES_CACHE_KEY, [])));
        };
        $viewFactory->composer('queue-monitor::status', $composer);
        $viewFactory->composer('queue-monitor::status-json', $composer);
    }
}
