<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Repository\Exception;

class BaseException extends \LogicException
{
    public function __construct($message = "An error occurred!", \Exception $previous = null)
    {
        parent::__construct($message, 202, $previous);
    }
}