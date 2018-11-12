<?php

namespace TomKriek\CopernicaAPI\Exceptions;

class BadCopernicaRequest extends \Exception
{
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
