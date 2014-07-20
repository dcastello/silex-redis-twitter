<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification\BeanstalkdQueue;

use Notification\QueueInterface;
use Pheanstalk\Pheanstalk;

class Queue implements QueueInterface
{
    private $pheanstalk;

    function __construct(Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }

    public function produce($tube, $message)
    {
        $this->pheanstalk->useTube($tube)->put(json_encode($message));
    }

    public function getNextJob($tube)
    {
        $job = $this->pheanstalk
            ->watch($tube)
            ->ignore(Tubes::TUBE_DEFAULT)
            ->reserve();

        return $job;
    }

    public function deleteJob($job)
    {
        $this->pheanstalk->delete($job);
    }
}