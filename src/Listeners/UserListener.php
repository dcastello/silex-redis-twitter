<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Listeners;

use Event\MessageAddedEvent;
use Event\UserFollowEvent;
use Event\UserUnfollowEvent;
use Notification\BeanstalkdQueue\Tubes;
use Notification\Producer;

class UserListener
{

    private $queue;

    public function __construct(Producer $queue)
    {
        $this->queue = $queue;
    }

    public function onMessageAdded(MessageAddedEvent $event)
    {
        $user = $event->getUser();
        $message = $event->getMessage();
        $data = array(
            'userId' => $user->getId(),
            'messageId' => $message->getId(),
            'postedAt' => $message->getPostedAt()
        );

        $this->queue->produce(Tubes::TUBE_MESSAGE_NEW, $data);

    }

    public function onUserFollow(UserFollowEvent $event)
    {
        $user = $event->getUser();
        $userToFollow = $event->getUserToFollow();
        $data = array('userId' => $user->getId(), 'userIdToFollow' => $userToFollow->getId());

        $this->queue->produce(Tubes::TUBE_USER_FOLLOW, $data);
    }

    public function onUserUnfollow(UserUnfollowEvent $event)
    {
        $user = $event->getUser();
        $userToUnfollow = $event->getUserToUnfollow();
        $data = array('userId' => $user->getId(), 'userIdToUnfollow' => $userToUnfollow->getId());

        $this->queue->produce(Tubes::TUBE_USER_UNFOLLOW, $data);
    }
} 