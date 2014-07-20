<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification;

interface ConsumerInterface
{
    public function process($job);
} 