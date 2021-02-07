<?php declare(strict_types=1);

namespace Lib\Tests\Service\Common {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OutboxException;
    use App\Api\Service\Parameters\MessagingParameters;
    use App\Service\Common\HttpMessagingService;
    use Error;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Helper\Identifier;
    use Lib\Tests\Helper\OnboardResponseRepository;
    use PHPUnit\Framework\TestCase;

    class HttpMessagingServiceTest extends TestCase
    {

        /**
         * @covers HttpMessagingService::send()
         */
        function testGivenInvalidParametersWhenSendingMessageViaHttpThenTheServiceShouldThrowAnException()
        {
            self::expectException(Error::class);

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());
            $parameters = new MessagingParameters();
            $httpMessagingService->send($parameters);
        }

        /**
         * @covers HttpMessagingService::send()
         */
        function testGivenInvalidMessageWhenSendingMessageViaHttpThenTheServiceShouldThrowAnException()
        {
            self::expectException(OutboxException::class);
            self::expectExceptionCode(intval(ErrorCodes::INVALID_MESSAGE));

            $onboardResponse = OnboardResponseRepository::read(Identifier::COMMUNICATION_UNIT);

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $httpMessagingService = new HttpMessagingService($guzzleHttpClientBuilder->build());

            $parameters = new MessagingParameters();
            $parameters->setOnboardResponse($onboardResponse);
            $parameters->setEncodedMessages(["SOME_ENCODED_MESSAGE"]);
            $httpMessagingService->send($parameters);
        }

    }
}