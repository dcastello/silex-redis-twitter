<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Event;

use Model\User;
use Symfony\Component\EventDispatcher\Event;

class UserFollowEvent extends Event
{

    private $user;
    private $userToFollow;

    function __construct(User $user, User $userToFollow)
    {
        $this->user = $user;
        $this->userToFollow = $userToFollow;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return User
     */
    public function getUserToFollow()
    {
        return $this->userToFollow;
    }

} 