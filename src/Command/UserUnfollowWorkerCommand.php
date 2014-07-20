<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../app.php";
require_once __DIR__ . "/../../config/prod.php";

$userUnfollowConsumer = $app['worker.unfollowuser'];

while (true) {
    $job = $userUnfollowConsumer->getNextJob();
    if (is_object($job)) {
        $userUnfollowConsumer->process($job);
    }
}