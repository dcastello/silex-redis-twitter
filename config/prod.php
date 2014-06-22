<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

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