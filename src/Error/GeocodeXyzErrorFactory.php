<?php

declare(strict_types=1);

namespace Cydrickn\Geocoder\Provider\GeocodeXyz\Error;

use Geocoder\Exception\Exception;

final class GeocodeXyzErrorFactory
{
    public static function create(string $code, string $description): Exception
    {
        $error = null;
        switch ($code) {
            case AuthNotFoundError::ERROR_CODE:
                $error = new AuthNotFoundError($description);
                break;
            case AuthRanOutError::ERROR_CODE:
                $error = new AuthRanOutError($description);
                break;
            case InvalidPostalCodeError::ERROR_CODE:
                $error = new InvalidPostalCodeError($description);
                break;
            case InvalidQuery::ERROR_CODE:
                $error = new InvalidQuery($description);
                break;
            case NoResultError::ERROR_CODE:
                $error = new NoResultError($description);
                break;
            default:
                $error = new GeneralError($description, $code);
                break;
        }

        return $error;
    }

    private function __construct()
    {
        // Empty, so that it will forcefully use the static create function
    }
}
