<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Manager;

use Repository\MessageRepository;
use Repository\UserRepository;

class UserManager
{

    /** @var \Repository\UserRepository */
    private $userRepository;
    /** @var \Repository\MessageRepository */
    private $messageRepository;

    function __construct(UserRepository $userRepository, MessageRepository $messageRepository)
    {
        $this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;
    }

    public function publishMessageOnBoardHome($userId, $messageId, $postedAt)
    {
        $this->messageRepository->addMessageToBoardHome($userId, $messageId, $postedAt);
    }

    public function publishMessageToAllFollowersOnBoardHome($userId, $messageId, $postedAt)
    {
        $followers = $this->userRepository->findAllFollowers($userId);

        foreach ($followers as $follower) {
            $this->publishMessageOnBoardHome($follower->getId(), $messageId, $postedAt);
        }
    }

    public function synchronizeTimelineWithUserFollowingMessages($userFromId, $userFollowingId)
    {
        $messages = $this->messageRepository->findMessagesForUser($userFollowingId);
        foreach ($messages as $message) {
            $postedAt = $message->getPostedAt()->format('YmdHis');
            $this->messageRepository->addMessageToBoardHome($userFromId, $message->getId(), $postedAt);
        }
    }

    public function synchronizeTimelineWithUnfollowingUser($userFromId, $userToUnfollowId)
    {
        $messages = $this->messageRepository->findMessagesForUser($userToUnfollowId);
        foreach ($messages as $message) {
            $this->messageRepository->removeMessageOnBoardHome($userFromId, $message->getId());
        }

    }
} 