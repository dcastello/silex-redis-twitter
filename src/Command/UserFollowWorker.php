<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

//sleep(10);

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../app.php";
require_once __DIR__ . "/../../config/prod.php";

use Notification\BeanstalkdQueue\Consumers\UserFollowConsumer;
use Notification\BeanstalkdQueue\Tubes;

$userFollowConsumer = new UserFollowConsumer($app['user.manager'], $app['queue.system'], Tubes::TUBE_USER_FOLLOW);

while (true) {
    $job = $userFollowConsumer->getNextJob();
    if (is_object($job)) {
        $userFollowConsumer->process($job);
    }
}