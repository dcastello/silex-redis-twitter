<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Repository;

use Model\Message;

interface MessageRepository
{
    public function insert(Message $message);

    public function findBoardForUser($userId, $board);
}