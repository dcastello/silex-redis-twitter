<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification\BeanstalkdQueue;

use Notification\Producer;
use Pheanstalk\Pheanstalk;

class Queue implements Producer
{
    private $queue;

    function __construct(Pheanstalk $queue)
    {
        $this->queue = $queue;
    }

    public function produce($tube, $message)
    {
        $this->queue->useTube($tube)->put(json_encode($message));
    }

    public function getNextJob($tube)
    {
        $job = $this->queue
            ->watch($tube)
            ->ignore(Tubes::TUBE_DEFAULT)
            ->reserve();

        return $job;
    }

    public function deleteJob($job)
    {
        $this->queue->delete($job);
    }
}