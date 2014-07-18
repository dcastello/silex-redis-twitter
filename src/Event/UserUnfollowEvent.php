<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Event;

use Model\User;
use Symfony\Component\EventDispatcher\Event;

class UserUnfollowEvent extends Event
{

    private $user;
    private $userToUnfollow;

    function __construct(User $user, User $userToUnfollow)
    {
        $this->user = $user;
        $this->userToUnfollow = $userToUnfollow;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \Model\User
     */
    public function getUserToUnfollow()
    {
        return $this->userToUnfollow;
    }

} 