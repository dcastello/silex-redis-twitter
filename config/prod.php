<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

use Event\StoreEvents;
use Listeners\UserListener;
use Manager\UserManager;
use Notification\BeanstalkdQueue;
use Notification\BeanstalkdQueue\Queue;
use Notification\BeanstalkdQueue\Tubes;
use Notification\BeanstalkdQueue\Workers\MessageWorker;
use Notification\BeanstalkdQueue\Workers\UserFollowWorker;
use Notification\BeanstalkdQueue\Workers\UserUnfollowWorker;
use Pheanstalk\Pheanstalk;
use Repository\RedisMessageRepository;
use Repository\RedisUserRepository;

$app['user.repository'] = $app->share(
    function () use ($app) {
        $predis = $app['predis'];

        return new RedisUserRepository($predis);
    }
);

$app['message.repository'] = $app->share(
    function () use ($app) {
        $predis = $app['predis'];

        return new RedisMessageRepository($predis);
    }
);

$app['queue.pheanstalk'] = $app->share(
    function () use ($app) {
        return new Pheanstalk('127.0.0.1');
    }
);

$app['queue.manager'] = $app->share(
    function () use ($app) {
        return new Queue($app['queue.pheanstalk']);
    }
);

$app['listener.user'] = $app->share(
    function () use ($app) {
        return new UserListener($app['queue.manager']);
    }
);

$app['user.manager'] = $app->share(
    function () use ($app) {
        return new UserManager($app['user.repository'], $app['message.repository']);
    }
);

$app['worker.message'] = $app->share(
    function () use ($app) {
        $userManager = $app['user.manager'];
        $queueSystem = $app['queue.manager'];

        return new MessageWorker($userManager, $queueSystem, Tubes::TUBE_MESSAGE_NEW);
    }
);

$app['worker.followuser'] = $app->share(
    function () use ($app) {
        $userManager = $app['user.manager'];
        $queueSystem = $app['queue.manager'];

        return new UserFollowWorker($userManager, $queueSystem, Tubes::TUBE_USER_FOLLOW);
    }
);

$app['worker.unfollowuser'] = $app->share(
    function () use ($app) {
        $userManager = $app['user.manager'];
        $queueSystem = $app['queue.manager'];

        return new UserUnfollowWorker($userManager, $queueSystem, Tubes::TUBE_USER_UNFOLLOW);
    }
);

$userListener = $app['listener.user'];
$app['dispatcher']->addListener(StoreEvents::MESSAGE_ADDED, array($userListener, 'onMessageAdded'));
$app['dispatcher']->addListener(StoreEvents::USER_FOLLOW, array($userListener, 'onUserFollow'));
$app['dispatcher']->addListener(StoreEvents::USER_UNFOLLOW, array($userListener, 'onUserUnfollow'));