<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Repository;

use Model\BoardTypes;
use Model\Message;
use Model\User;

class RedisMessageRepository implements MessageRepository
{
    private $manager;

    function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Message $message
     * @return \Model\Message
     */
    public function insert(Message $message)
    {
        $pipeline = $this->manager->pipeline();

        $pipeline->incr("status:id");
        $result = $pipeline->execute();
        $statusId = $result[0];

        $statusData = $this->convertMessageToKey($message);
        $message->setId($statusId);
        $statusData['id'] = $statusId;

        $pipeline->hmset("status:$statusId", $statusData);
        $pipeline->hincrby("user:" . $message->getUser()->getId(), "posts", 1);
        $pipeline->execute();

        // ZADD KEY SCORE MEMBER
        $this->manager->zadd(
            "profile:" . $message->getUser()->getId(),
            $message->getPostedAt(),
            $message->getId()
        );

        return $message;
    }

    /**
     * @param $userId
     * @param string $board
     * @return array|string
     */
    public function findBoardForUser($userId, $board = BoardTypes::BOARD_PROFILE)
    {
        $statusesAsHash = $this->manager->zrevrange("$board:$userId", 0, -1);

        $board = array();
        foreach ($statusesAsHash as $statusId) {
            $statusHash = $this->manager->hgetall("status:$statusId");
            $board[] = $this->convertKeyToMessage($statusHash);
        }

        return $board;
    }

    public function addMessageToBoardHome($userId, $messageId, $postedAt)
    {
        $pipeline = $this->manager->pipeline();

        // ZADD KEY SCORE MEMBER
        $key = BoardTypes::BOARD_HOME . ":$userId";
        $pipeline->zadd($key, $postedAt, $messageId);

        $pipeline->execute();
    }

    public function removeMessageOnBoardHome($userId, $messageId)
    {
        $pipeline = $this->manager->pipeline();

        // ZREM KEY MEMBER
        $key = BoardTypes::BOARD_HOME . ":$userId";
        $pipeline->zrem($key, $messageId);

        $pipeline->execute();
    }

    public function findById($messageId)
    {
        $messageHash = $this->manager->hgetall('status:' . $messageId);

        if (count($messageHash) == 0) {
            return false;
        }

        $message = $this->convertKeyToMessage($messageHash);

        return $message;
    }


    public function findMessagesForUser($userId)
    {
        $result = array();

        $board = BoardTypes::BOARD_PROFILE . ":$userId";
        $statusesWithScore = $this->manager->zrevrange($board, 0, -1, array('withscores' => true));
        foreach ($statusesWithScore as $status) {
            $messageId = $status[0];
            $result[] = $this->findById($messageId);
        }

        return $result;
    }

    private function convertKeyToMessage($messageData)
    {
        $message = new Message();
        $message->setId($messageData['id']);
        $message->setMessage($messageData['message']);
        $posted = \DateTime::createFromFormat('Ymdhsi', $messageData['posted']);
        $message->setPostedAt($posted);

        $pipeline = $this->manager->pipeline();
        $pipeline->hgetall('user:' . $messageData['uid']);
        $result = $pipeline->execute();

        $auxUser = $result[0];
        $user = new User();
        $user->setId($auxUser['id']);
        $user->setLogin($auxUser['login']);
        $user->setName($auxUser['name']);
        $user->setFollowers($auxUser['followers']);
        $user->setFollowing($auxUser['following']);
        $user->setPosts($auxUser['posts']);
        $user->setSignup($auxUser['signup']);
        $message->setUser($user);

        return $message;
    }

    private function convertMessageToKey(Message $message)
    {
        $statusData = array(
            'message' => $message->getMessage(),
            'posted' => $message->getPostedAt(),
            'id' => $message->getId(),
            'uid' => $message->getUser()->getId(),
            'login' => $message->getUser()->getLogin()
        );

        return $statusData;
    }

} 