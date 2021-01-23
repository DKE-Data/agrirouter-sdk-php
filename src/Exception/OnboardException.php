<?php declare(strict_types=1);

namespace App\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Will be thrown if there is an error during the onboarding process.
 * @package App\Exception
 */
class OnboardException extends Exception
{
    #[Pure] public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

