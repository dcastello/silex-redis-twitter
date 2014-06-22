<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Model;

class User
{

    private $id;
    private $login;
    private $name;
    private $followers = 0;
    private $following = 0;
    private $posts = 0;
    private $signup;

    /**
     * @param mixed $followers
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
    }

    /**
     * @return mixed
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param mixed $following
     */
    public function setFollowing($following)
    {
        $this->following = $following;
    }

    /**
     * @return mixed
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $signup
     */
    public function setSignup($signup)
    {
        $this->signup = $signup;
    }

    /**
     * @return mixed
     */
    public function getSignup()
    {
        return $this->signup;
    }


} 