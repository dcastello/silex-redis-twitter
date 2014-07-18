<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Repository;

use Model\Message;

interface MessageRepository
{
    public function insert(Message $message);

    public function findById($messageId);

    public function findBoardForUser($userId, $board);

    public function addMessageToBoardHome($userId, $messageId, $postedAt);

    public function removeMessageOnBoardHome($userId, $messageId);

    public function findMessagesForUser($userId);
}