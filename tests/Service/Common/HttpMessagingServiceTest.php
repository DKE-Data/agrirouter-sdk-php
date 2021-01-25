<?php


namespace Lib\Tests\Service\Common {


    use App\Api\Exceptions\ValidationException;
    use App\Api\Service\Parameters\MessagingParameters;
    use App\Exception\ErrorCodes;
    use App\Service\Common\HttpMessagingService;
    use Lib\Tests\Helper\HttpClientFactory;
    use PHPUnit\Framework\TestCase;

    class HttpMessagingServiceTest extends TestCase
    {

        /**
         * @covers HttpMessagingService::send()
         */
        function testGivenInvalidParametersWhenSendingMessageViaHttpThenTheServiceShouldThrowAnException()
        {
            self::expectException(ValidationException::class);
            self::expectExceptionCode(ErrorCodes::PARAMETER_INVALID);

            $httpMessagingService = new HttpMessagingService(HttpClientFactory::authenticatedHttpClient());
            $parameters = new MessagingParameters();
            $httpMessagingService->send($parameters);
        }

    }
}