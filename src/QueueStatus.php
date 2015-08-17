<?php
namespace Tremby\QueueMonitor;

use Cache;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class QueueStatus implements
    Arrayable,
    JsonSerializable,
    Jsonable
{
    const ERROR = 'error';
    const PENDING = 'pending';
    const OK = 'ok';

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var Carbon
     */
    protected $startTime = null;

    /**
     * @var Carbon
     */
    protected $endTime = null;

    /**
     * Make a new instance
     *
     * @param string $queueName Queue name
     * @param string $status Status constant
     * @param bool $startTime Whether to set the start time as the current time
     */
    public function __construct($queueName, $status, $startTime = true)
    {
        $this->queueName = $queueName;
        $this->status = $status;
        if ($startTime) {
            $this->startTime = Carbon::now();
        }
    }

    /**
     * Get the queue name
     *
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Is this queue pending its queued job?
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->getStatus() === self::PENDING;
    }

    /**
     * Is this queue OK?
     *
     * @return bool
     */
    public function isOk()
    {
        return $this->getStatus() === self::OK;
    }

    /**
     * Does this queue have an error status?
     *
     * @return bool
     */
    public function isError()
    {
        return $this->getStatus() === self::ERROR;
    }

    /**
     * Set the status
     *
     * @param string $status Status constant
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get the start time
     *
     * @return Carbon
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Get the end time
     *
     * @return Carbon
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set the end time
     *
     * @param Carbon $endTime End time, or null for the current time
     * @return void
     */
    public function setEndTime(Carbon $endTime = null)
    {
        if ($endTime) {
            $this->endTime = $endTime;
        } else {
            $this->endTime = Carbon::now();
        }
    }

    /**
     * Get the message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the message
     *
     * @param string $message
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Save in cache
     *
     * @return void
     */
    public function save()
    {
        Cache::put(QueueMonitor::getCheckCacheKey($this->getQueueName()), $this, QueueMonitor::CACHE_TIME);
    }

    /**
     * {@inheritDoc}
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'queueName' => $this->getQueueName(),
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'startTime' => $this->getStartTime(),
            'startTimeDiff' => $this->getStartTime() ? $this->getStartTime()->diffInSeconds(null, false) : null,
            'endTime' => $this->getEndTime(),
            'endTimeDiff' => $this->getEndTime() ? $this->getEndTime()->diffInSeconds(null, false) : null,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Get an instance from cache by queue name
     *
     * @param string $queueName
     * @return QueueStatus
     */
    public static function get($queueName)
    {
        return Cache::get(QueueMonitor::getCheckCacheKey($queueName));
    }
}
