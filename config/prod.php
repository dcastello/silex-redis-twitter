<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

use Event\StoreEvents;
use Listeners\UserListener;
use Manager\UserManager;
use Notification\BeanstalkdQueue;
use Notification\BeanstalkdQueue\Queue;
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

$app['queue.system'] = $app->share(
    function () use ($app) {
        return new Queue($app['queue.pheanstalk']);
    }
);

$app['listener.user'] = $app->share(
    function () use ($app) {
        return new UserListener($app['queue.system']);
    }
);

$app['user.manager'] = $app->share(
    function () use ($app) {
        return new UserManager($app['user.repository'], $app['message.repository']);
    }
);

$userListener = $app['listener.user'];
$app['dispatcher']->addListener(StoreEvents::MESSAGE_ADDED, array($userListener, 'onMessageAdded'));
$app['dispatcher']->addListener(StoreEvents::USER_FOLLOW, array($userListener, 'onUserFollow'));