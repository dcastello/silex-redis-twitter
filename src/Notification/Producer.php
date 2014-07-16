<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification;

interface Producer
{
    public function produce($tube, $message);
}