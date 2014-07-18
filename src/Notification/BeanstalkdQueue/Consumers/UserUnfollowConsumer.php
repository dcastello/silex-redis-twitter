<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification\BeanstalkdQueue\Consumers;


use Manager\UserManager;
use Notification\BeanstalkdQueue\Queue;
use Notification\Consumer;

class UserUnfollowConsumer extends Worker implements Consumer
{

    private $userManager;

    function __construct(UserManager $userManager, Queue $queue, $tube)
    {
        parent::__construct($queue, $tube);
        $this->userManager = $userManager;
    }


    public function process($job)
    {
        $data = json_decode($job->getData());
        $this->userManager->synchronizeTimelineWithUnfollowingUser($data->userId, $data->userIdToUnfollow);
        $this->getQueue()->deleteJob($job);
    }

    function getNextJob()
    {
        return $this->getQueue()->getNextJob($this->getTube());
    }
}