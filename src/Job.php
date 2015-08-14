<?php
namespace Tremby\QueueMonitor;

use Carbon\Carbon;
use Illuminate\Queue\Jobs\Job as IlluminateJob;
use Log;

class Job
{
    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var Carbon
     */
    protected $startTime;

    /**
     * Execute the job
     *
     * @param Illuminate\Queue\Jobs\Job $job
     * @param array $data
     * @return void
     */
    public function handle(IlluminateJob $job, $data)
    {
        $this->queueName = $data['queueName'];
        $this->startTime = new Carbon($data['startTime']['date'], $data['startTime']['timezone']);

        Log::debug("Handling check job for queue '{$this->queueName}' queued at {$this->startTime}");
        $status = QueueStatus::get($this->queueName);
        if (!$status) {
            $message = "Queue status was not found in cache, yet queued job ran; is the cache correctly configured?";
            Log::error($message);
            $status = new QueueStatus($this->queueName, QueueStatus::ERROR, false);
            $status->setMessage($message);
            $status->setEndTime();
            $status->save();
        } elseif (!$status->isPending()) {
            Log::warning("Non-pending status for check for queue '{$this->queueName}' found in the cache; ignoring: " . $status);
        } elseif (!$status->getStartTime() || $status->getStartTime()->ne($this->startTime)) {
            Log::warning("Pending status for check for queue '{$this->queueName}' found in the cache with mismatching time (expected {$this->startTime}, found {$status->getStartTime()}); ignoring: " . $status);
        } else {
            Log::debug("Successful queue check for queue '{$this->queueName}'");
            $status->setStatus(QueueStatus::OK);
            $status->setEndTime();
            $status->save();
        }
    }
}
