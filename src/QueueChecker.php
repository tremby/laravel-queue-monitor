<?php
namespace Tremby\QueueMonitor;

use Cache;
use Carbon\Carbon;
use Config;
use Queue;

abstract class QueueChecker
{
    const CACHE_KEY_PREFIX = 'queue-monitor:';
    const CACHE_TIME = Carbon::MINUTES_PER_HOUR * Carbon::HOURS_PER_DAY;
    const CHECK_STATUS_ERROR = 'error';
    const CHECK_STATUS_PENDING = 'pending';
    const CHECK_STATUS_OK = 'ok';

    /**
     * @var string
     */
    private $queue;

    /**
     * @var int
     */
    private $time;

    /**
     * Make a new instance
     *
     * @param string $queue Queue connection
     */
    public function __construct($queue)
    {
        $this->queue = $queue;
        $this->time = time();
    }

    /**
     * Get the queue connection name
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Get the time when this check was requested
     *
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Get the cache key for the check
     *
     * @return string
     */
    public function getCheckCacheKey()
    {
        return self::CACHE_KEY_PREFIX . $this->getQueue();
    }

    /**
     * Cache the fact that a queue check has been queued, and queue the check
     *
     * @return void
     */
    public function queueCheck()
    {
        $queueConfig = Config::get('queue.connections.' . $this->getQueue());

        Cache::put($this->getCheckCacheKey(), [
            'status' => self::CHECK_STATUS_PENDING,
            'time' => $this->getTime(),
        ], self::CACHE_TIME);

        Queue::pushOn(
            $this->getQueue(),
            new Job($this->getQueue(), $this->getTime(), $this->getCheckCacheKey())
        );
    }
}
