<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Notification\BeanstalkdQueue;

final class Tubes
{
    const TUBE_DEFAULT = 'default';
    const TUBE_MESSAGE_NEW = 'tube.message.new';
    const TUBE_USER_FOLLOW = 'tube.user.follow';
} 