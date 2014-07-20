<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../app.php";
require_once __DIR__ . "/../../config/prod.php";

$userFollowConsumer = $app['worker.followuser'];

while (true) {
    $job = $userFollowConsumer->getNextJob();
    if (is_object($job)) {
        $userFollowConsumer->process($job);
    }
}