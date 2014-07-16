<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification;

interface Consumer
{
    public function process($job);
} 