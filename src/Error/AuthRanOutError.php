<?php

namespace Cydrickn\Geocoder\Provider\GeocodeXyz\Error;

use Geocoder\Exception\Exception;

final class AuthRanOutError extends \RuntimeException implements Exception
{
    const ERROR_CODE = '002';

    public function __construct(string $message = "")
    {
        parent::__construct($message, self::ERROR_CODE);
    }
}
