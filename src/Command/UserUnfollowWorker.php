<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../app.php";
require_once __DIR__ . "/../../config/prod.php";

use Notification\BeanstalkdQueue\Consumers\UserUnfollowConsumer;
use Notification\BeanstalkdQueue\Tubes;

$userUnfollowConsumer = new UserUnfollowConsumer($app['user.manager'], $app['queue.system'], Tubes::TUBE_USER_UNFOLLOW);

while (true) {
    $job = $userUnfollowConsumer->getNextJob();
    if (is_object($job)) {
        $userUnfollowConsumer->process($job);
    }
}