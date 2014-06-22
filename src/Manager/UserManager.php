<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Manager;


use Model\User;

class UserManager {

    /**
     * @param $params
     * @return User
     */
    public function createUser($params)
    {
        $user = new User();
        $user->setName($params['name']);
        $user->setFollowers(0);
        $user->setFollowing(0);

        return $user;
    }
} 