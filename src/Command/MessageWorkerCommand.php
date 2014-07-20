<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../app.php";
require_once __DIR__ . "/../../config/prod.php";

$messageConsumer = $app['worker.message'];

while (true) {
    $job = $messageConsumer->getNextJob();
    if (is_object($job)) {
        $messageConsumer->process($job);
    }
}