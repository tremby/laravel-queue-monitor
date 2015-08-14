<?php
namespace Tremby\QueueMonitor\Exception;

class NoSuchQueueException extends QueueMonitorException
{
    private $_queue;

    public function __construct($queue)
    {
        $this->_queue = $queue;
        parent::__construct("No such queue '$queue'");
    }

    /**
     * Get the queue name
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->_queue;
    }
}
