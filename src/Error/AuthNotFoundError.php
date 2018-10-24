<?php

declare(strict_types=1);

namespace Cydrickn\Geocoder\Provider\GeocodeXyz\Error;

use Geocoder\Exception\Exception;

class AuthNotFoundError extends \RuntimeException implements Exception
{
    const ERROR_CODE = '003';

    public function __construct(string $message = "")
    {
        parent::__construct($message, self::ERROR_CODE);
    }
}
