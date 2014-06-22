<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

namespace Repository\Exception;

class UserExistException extends BaseException
{
    public function __construct($message = "User exist!", \Exception $previous = null)
    {
        parent::__construct($message, $previous); //
    }
} 