<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public $isResponseJson = true;
}
