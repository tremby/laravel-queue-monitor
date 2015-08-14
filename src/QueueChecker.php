<?php
namespace Tremby\QueueMonitor;

use Carbon\Carbon;
use Queue;

class QueueChecker
{
    /**
     * @var string
     */
    private $queueName;

    /**
     * @var int
     */
    private $startTime;

    /**
     * Make a new instance
     *
     * @param string $queueName Queue name
     */
    public function __construct($queueName)
    {
        $this->queueName = $queueName;
        $this->startTime = Carbon::now();
    }

    /**
     * Cache the fact that a queue check has been queued, and queue the check
     *
     * @return void
     */
    public function queueCheck()
    {
        $status = new QueueStatus($this->queueName, QueueStatus::PENDING);
        $status->save();

        Queue::push(
            'Tremby\QueueMonitor\Job@handle',
            [
                'queueName' => $this->queueName,
                'startTime' => $this->startTime,
            ],
            $this->queueName
        );
    }
}
