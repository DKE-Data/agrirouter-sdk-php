<?php


namespace Lib\Tests\Service\Common {


    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\ValidationException;
    use App\Api\Service\Parameters\MessagingParameters;
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
            self::expectException(\Error::class);

            $httpMessagingService = new HttpMessagingService(HttpClientFactory::httpClient());
            $parameters = new MessagingParameters();
            $httpMessagingService->send($parameters);
        }

    }
}