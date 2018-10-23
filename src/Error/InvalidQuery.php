<?php

namespace Cydrickn\Geocoder\Provider\GeocodeXyz\Error;

use Geocoder\Exception\Exception;

class InvalidQuery extends \RuntimeException implements Exception
{
    const ERROR_CODE = '007';

    public function __construct(string $message = "")
    {
        parent::__construct($message, self::ERROR_CODE);
    }
}
