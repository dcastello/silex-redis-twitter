<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

use Silex\Application;
use Predis\Silex\PredisServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

$app = new Application();

$app->register(new UrlGeneratorServiceProvider());

$app->register(
    new PredisServiceProvider(),
    array(
        'predis.parameters' => 'tcp://127.0.0.1:6379'
    )
);

$app->register(
    new TwigServiceProvider(),
    array(
        'twig.path' => array(__DIR__ . '/../views')
    )
);

$app->register(new SessionServiceProvider());
