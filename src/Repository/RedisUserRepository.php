<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Repository;

use Model\User;
use Repository\Exception\UserExistException;

class RedisUserRepository implements UserRepository
{
    private $manager;

    function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $userId
     * @return bool|User
     */
    public function findById($userId)
    {
        $userHash = $this->manager->hgetall("user:" . $userId);

        if (count($userHash) == 0) {
            return false;
        }

        $user = new User();
        $user->setId($userHash['id']);
        $user->setName($userHash['name']);
        $user->setLogin($userHash['login']);
        $user->setFollowers($userHash['followers']);
        $user->setFollowing($userHash['following']);
        $user->setPosts($userHash['posts']);
        $signup = \DateTime::createFromFormat('Ymdhsi', $userHash['signup']);
        $user->setSignup($signup);

        return $user;
    }

    /**
     * @param $login
     * @return bool|User
     */
    public function findByLogin($login)
    {
        $userId = $this->manager->hget("users:", $login);

        if (is_null($userId)) {
            return false;
        }

        return $this->findById($userId);
    }

    public function findAllFollowing($userId)
    {
        $following = $this->manager->zrevrange('following:' . $userId, 0, -1);
        $users = array();

        foreach ($following as $userId) {
            $users[] = $this->findById($userId);
        }

        return $users;
    }

    public function findAllFollowers($userId)
    {
        $followers = $this->manager->zrevrange('followers:' . $userId, 0, -1);
        $users = array();
        foreach ($followers as $userId) {
            $users[] = $this->findById($userId);
        }

        return $users;
    }

    /**
     * @param User $user
     * @throws Exception\UserExistException
     */
    public function insertUser(User $user)
    {
        $lowerLogin = strtolower($user->getLogin());
        $existUser = $this->manager->hget("users:", $lowerLogin);

        if (is_null($existUser) === false) {
            throw new UserExistException();
        }

        $id = $this->manager->incr("user:id");

        $pipeline = $this->manager->pipeline();
        $pipeline->hset("users:", $lowerLogin, $id);
        $pipeline->hmset(
            "user:$id",
            array(
                'login' => $user->getLogin(),
                'id' => $id,
                'name' => $user->getName(),
                'followers' => $user->getFollowers(),
                'following' => $user->getFollowing(),
                'posts' => $user->getPosts(),
                'signup' => date('Ymdhsi')
            )
        );
        $pipeline->execute();
    }

    /**
     * @param $id
     * @return bool
     */
    public function existUser($id)
    {
        $login = strtolower("login" . $id);
        $existUser = $this->manager->hget("users:", $login);

        return (is_null($existUser) !== true);
    }

    public function followUser($userId, $userIdToFollow)
    {
        $isFollowingUser = $this->manager->zscore('following:' . $userId, $userIdToFollow);
        if (null !== $isFollowingUser) {
            return false;
        }

        $pipeline = $this->manager->pipeline();

        // Incrementamos followers y followings para cada usuario
        $now = date('Ymdhsi');

        // ZADD KEY SCORE MEMBER ==> ZADD following:15 $now $userIdToFollow
        $pipeline->zadd("following:$userId", $now, $userIdToFollow);
        $pipeline->zadd("followers:$userIdToFollow", $now, $userId);
        $pipeline->zcard("following:$userId");
        $pipeline->zcard("followers:$userIdToFollow");

        $results = $pipeline->execute();

        $totalFollowing = $results[2];
        $totalFollowers = $results[3];

        $pipeline->hset("user:$userId", "following", $totalFollowing);
        $pipeline->hset("user:$userIdToFollow", "followers", $totalFollowers);

        $pipeline->execute();
    }

    public function unfollowUser($userId, $userIdToUnfollow)
    {
        $isFollowingUser = $this->manager->zscore('following:' . $userId, $userIdToUnfollow);
        if (null === $isFollowingUser) {
            return false;
        }

        $pipeline = $this->manager->pipeline();

        $pipeline->zrem("following:$userId", $userIdToUnfollow);
        $pipeline->zrem("followers:$userIdToUnfollow", $userId);
        $pipeline->zcard("following:$userId");
        $pipeline->zcard("followers:$userIdToUnfollow");
        $pipeline->zrevrange("profile:$userIdToUnfollow", 0, -1);

        $results = $pipeline->execute();

        $totalFollowing = $results[2];
        $totalFollowers = $results[3];
        $statuses = $results[4];

        $pipeline->hset("user:$userId", "following", $totalFollowing);
        $pipeline->hset("user:$userIdToUnfollow", "followers", $totalFollowers);

        foreach ($statuses as $status) {
            // ZREM KEY MEMBER
            $pipeline->zrem("home:$userId", $status[0]);
        }

        $pipeline->execute();
    }

    public function findAll()
    {
        $result = array();

        $users = $this->manager->hgetall('users:');
        foreach ($users as $userId) {
            $result[] = $this->findById($userId);
        }

        return $result;
    }


} 