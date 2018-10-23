<?php

namespace Cydrickn\Geocoder\Provider\GeocodeXyz\Error;

use Geocoder\Exception\Exception;

class NoResultError extends \RuntimeException implements Exception
{
    const ERROR_CODE = '008';

    public function __construct(string $message = "")
    {
        parent::__construct($message, self::ERROR_CODE);
    }
}
