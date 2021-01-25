<?php declare(strict_types=1);

namespace App\Api\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Will be thrown if there is an error during the onboarding process.
 * @package App\Exception
 */
class OnboardException extends BusinessException
{

    /**
     * OnboardException constructor.
     * @param string $message The message.
     * @param string $code The code.
     */
    #[Pure] public function __construct(string $message, string $code)
    {
        parent::__construct($message, $code);
    }
}

