<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification\BeanstalkdQueue\Workers;

use Notification\BeanstalkdQueue\Queue;

abstract class Worker
{
    private $queue;
    private $tube;

    public function __construct(Queue $queue, $tube)
    {
        $this->queue = $queue;
        $this->tube = $tube;
    }

    /**
     * @return \Notification\BeanstalkdQueue\Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    public function getTube()
    {
        return $this->tube;
    }

    abstract function getNextJob();

}