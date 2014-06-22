<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Repository;

use Model\User;

interface UserRepository
{
    public function findById($userId);

    public function findByLogin($login);

    public function findAll();

    public function findAllFollowing($userId);

    public function findAllFollowers($userId);

    public function insertUser(User $user);

    public function existUser($id);

    public function followUser($userId, $userIdToFollow);

    public function unfollowUser($userId, $userIdToUnfollow);
} 