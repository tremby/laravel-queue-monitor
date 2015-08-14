<?php
namespace Tremby\QueueMonitor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class Job implements SelfHandling, ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;

    protected $queue;
    protected $time;
    protected $checkCacheKey;

    /**
     * Make a new instance
     *
     * @param string $queue Queue connection name
     * @param string $time Queue checker start time
     * @param string $checkCacheKey Check cache key
     * @return void
     */
    public function __construct($queue, $time, $checkCacheKey)
    {
        $this->queue = $queue;
        $this->time = $time;
        $this->checkCacheKey = $checkCacheKey;
    }

    /**
     * Execute the job
     *
     * @param CacheRepository $cache
     * @param ConfigRepository $config
     * @return void
     */
    public function handle(CacheRepository $cache, ConfigRepository $config, Log $log)
    {
        $log->debug("Handling check job for queue '{$this->queue}' started at time $time");
        $queueConfig = $config->get('queue.connections.' . $this->queue);
        $data = $cache->get($this->checkCacheKey);
        if (!$data) {
            $message = "Data for check was not found in cache";
            $log->error($message);
            $data = [
                'finish_time' => time(),
                'status' => QueueChecker::CHECK_STATUS_ERROR,
                'message' => $message,
            ];
            $cache->put($this->checkCacheKey, $data, QueueChecker::CACHE_TIME);
        } elseif ($data['status'] != QueueChecker::CHECK_STATUS_PENDING) {
            $log->warning("Non-pending status for check for queue '{$this->queue}' found in the cache; ignoring: " . print_r($data, true));
        } elseif ($data['time'] != $this->time) {
            $log->warning("Pending status for check for queue '{$this->queue}' found in the cache with mismatching time (expected {$this->time}, found {$data['time']}); ignoring: " . print_r($data, true));
        } else {
            $log->debug("Successful queue check for queue '{$this->queue}'");
            $data['status'] = QueueChecker::CHECK_STATUS_OK;
            $data['finish_time'] = time();
            $cache->put($this->checkCacheKey, $data, QueueChecker::CACHE_TIME);
        }
    }
}
