<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    public function __construct($message = '', $code = 0, Throwable $previouse = null)
    {

        parent::__construct($message, $code, $previouse);
        if (empty($message)) {
            switch ($code) {
                case 1:
                $this->message = 'Poll not found';
                    break;

                default:
                    $this->message = 'unknown error';
                    break;
            }
        }
    }

    public function report()
    {
        \Log::error('__API__: ' . $this->getMessage());
    }
}
