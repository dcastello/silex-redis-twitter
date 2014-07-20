<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification;


interface QueueInterface
{
    function produce($tube, $message);

    function getNextJob($tube);

    function deleteJob($tube);
} 