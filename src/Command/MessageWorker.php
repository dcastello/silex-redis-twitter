<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

//sleep(10);

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../app.php";
require_once __DIR__ . "/../../config/prod.php";

use Notification\BeanstalkdQueue\Consumers\MessageConsumer;
use Notification\BeanstalkdQueue\Tubes;

$messageConsumer = new MessageConsumer($app['user.manager'], $app['queue.system'], Tubes::TUBE_MESSAGE_NEW);

while (true) {
    $job = $messageConsumer->getNextJob();
    if (is_object($job)) {
        $messageConsumer->process($job);
    }
}