<?php
namespace Tremby\QueueMonitor;

use Cache;
use Carbon\Carbon;
use Config;

class QueueMonitor
{
    const CACHE_TIME = Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;
    const CACHE_KEY_PREFIX = 'queue-monitor:';
    const QUEUES_CACHE_KEY = self::CACHE_KEY_PREFIX . 'queues';

    /**
     * Get a cache key for the check status of a queue by name
     *
     * @param string $queueName
     * @return string
     */
    public static function getCheckCacheKey($queueName)
    {

        return self::CACHE_KEY_PREFIX . 'status:' . $queueName;
    }

    /**
     * Queue a queue check
     *
     * @param string $queueName Queue to queue a check for, or null for the
     * application's default queue
     * @return void
     */
    public static function queueQueueCheck($queueName = null)
    {
        // Get the default queue name if none was given
        if (is_null($queueName)) {
            $queueName = Config::get('queue.connections.' . Config::get('queue.default') . '.queue');
        }

        // Store this queue name in the monitored queues list
        $queues = Cache::get(self::QUEUES_CACHE_KEY, []);
        if (!in_array($queueName, $queues)) {
            $queues[] = $queueName;
            Cache::put(self::QUEUES_CACHE_KEY, $queues, self::CACHE_TIME);
        }

        // Add a queue job for this queue
        $checker = new QueueChecker($queueName);
        $checker->queueCheck();
    }
}
