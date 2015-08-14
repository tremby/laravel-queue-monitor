<?php
namespace Tremby\QueueMonitor;

use Config;

class QueueMonitor
{
    /**
     * Queue a queue check
     *
     * @param string $queue Queue to queue a check for, or null for the
     * application's default queue
     * @return void
     *
     * @throws Exception\NoSuchQueueException if no such queue exists
     */
    public static function queueQueueCheck($queue = null)
    {
        \Log::debug("queueing queue check");
        if (is_null($queue)) {
            $queue = Config::get('queue.default');
        }
        \Log::debug("queue: $queue");

        $queueConfig = Config::get("queue.connections.$queue");

        if (!$queueConfig) {
            throw new Exception\NoSuchQueueException($queue);
        }

        $checker = new QueueChecker($queue);
        $checker->queueCheck();
    }
}
