<?php declare(strict_types=1);

namespace Lib\Tests\Helper\Exception {

    use App\Api\Exceptions\BusinessException;
    use JetBrains\PhpStorm\Pure;

    /**
     * Will be thrown if there is an error during the build process of a routing device onboard response.
     * @package App\Exception
     */
    class RoutingDeviceOnboardResponseBuilderException extends BusinessException
    {

        /**
         * Constructor.
         * @param string $message The message.
         * @param int $code The code.
         */
        #[Pure] public function __construct(string $message, int $code)
        {
            parent::__construct($message, $code);
        }
    }
}

