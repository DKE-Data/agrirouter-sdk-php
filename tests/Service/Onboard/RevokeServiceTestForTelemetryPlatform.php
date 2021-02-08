<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\RevokeException;
    use App\Api\Exceptions\SignatureException;
    use App\Service\Common\UuidService;
    use App\Service\Onboard\RevokeService;
    use App\Service\Parameters\RevokeParameters;
    use DateTime;
    use DateTimeZone;
    use Lib\Tests\Applications\TelemetryPlatform;
    use Lib\Tests\Helper\GuzzleHttpClientBuilder;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;

    class RevokeServiceTestForTelemetryPlatform extends AbstractIntegrationTestForServices
    {
        private const ACCOUNT_ID = "5d47a537-9455-410d-aa6d-fbd69a5cf990";

        /**
         * @covers \App\Service\Onboard\RevokeService::revoke
         * @throws RevokeException
         * @throws SignatureException
         */
        public function testGivenNonExistingEndpointWhenRevokingTelemetryPlatformThenThereShouldBeAnException()
        {
            self::expectException(RevokeException::class);
            self::expectExceptionCode(ErrorCodes::INVALID_MESSAGE);

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $revokeService = new RevokeService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $revokeParameters = new RevokeParameters();
            $revokeParameters->setEndpointIds(array(UuidService::newUuid(), UuidService::newUuid()));
            $revokeParameters->setAccountId(self::ACCOUNT_ID);
            $revokeParameters->setApplicationId(TelemetryPlatform::applicationId());
            $revokeParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            $revokeService->revoke($revokeParameters, TelemetryPlatform::privateKey());
        }

        /**
         * @covers \App\Service\Onboard\RevokeService::revoke
         * @throws RevokeException
         * @throws SignatureException
         * @noinspection PhpUnreachableStatementInspection
         * @noinspection PhpVoidFunctionResultUsedInspection
         */
        public function testGivenExistingEndpointWhenRevokingTelemetryPlatformThenThereShouldBeAValidResponse()
        {
            $this->markTestSkipped('Will only run if there is an existing endpoint with the given endpoint ID.');

            $guzzleHttpClientBuilder = new GuzzleHttpClientBuilder();
            $revokeService = new RevokeService($this->getEnvironment(), $guzzleHttpClientBuilder->build());
            $revokeParameters = new RevokeParameters();
            $revokeParameters->setEndpointIds(array('72fd03ff-5e8e-4f6e-a54f-0aed8cec18f0'));
            $revokeParameters->setAccountId(self::ACCOUNT_ID);
            $revokeParameters->setApplicationId(TelemetryPlatform::applicationId());
            $revokeParameters->setOffset(timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime()));
            self::assertNull($revokeService->revoke($revokeParameters, TelemetryPlatform::privateKey()));
        }
    }
}