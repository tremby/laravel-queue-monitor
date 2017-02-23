<?php
namespace Tremby\QueueMonitor;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class Job implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var Carbon
     */
    protected $startTime;

    /**
     * Make a new instance
     *
     * @param string $queueName Queue connection name
     * @param Carbon $startTime Queue checker start time
     * @return void
     */
    public function __construct($queueName, Carbon $startTime)
    {
        $this->queueName = $queueName;
        $this->startTime = $startTime;
    }

    /**
     * Execute the job
     *
     * @param Log $log
     * @return void
     */
    public function handle(Log $log)
    {
        $log->debug("Handling check job for queue '{$this->queueName}', queued at {$this->startTime}");
        $status = QueueStatus::get($this->queueName);
        if (!$status) {
            $message = "Queue status was not found in cache, yet queued job ran; is the cache correctly configured?";
            $log->error($message);
            $status = new QueueStatus($this->queueName, QueueStatus::ERROR, false);
            $status->setMessage($message);
            $status->setEndTime();
            $status->save();
        } elseif (!$status->isPending()) {
            $log->warning("Non-pending status for check for queue '{$this->queueName}' found in the cache; ignoring: " . $status);
        } elseif (!$status->getStartTime() || $status->getStartTime()->ne($this->startTime)) {
            $log->warning("Pending status for check for queue '{$this->queueName}' found in the cache with mismatching time (expected {$this->startTime}, found {$status->getStartTime()}); ignoring: " . $status);
        } else {
            $log->debug("Successful queue check for queue '{$this->queueName}'");
            $status->setStatus(QueueStatus::OK);
            $status->setEndTime();
            $status->save();
        }
    }
}
