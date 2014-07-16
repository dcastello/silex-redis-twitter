<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Event;

use Model\Message;
use Model\User;
use Symfony\Component\EventDispatcher\Event;

class MessageAddedEvent extends Event
{

    private $user;
    private $message;

    function __construct(User $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

}