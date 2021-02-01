<?php declare(strict_types=1);

namespace Lib\Tests\Service\Onboard {

    use App\Api\Exceptions\AuthorizationException;
    use App\Service\Onboard\AuthorizationService;
    use Lib\Tests\Service\AbstractIntegrationTestForServices;

    class AuthorizationServiceTest extends AbstractIntegrationTestForServices
    {

        const APPLICATION_ID = "16b1c3ab-55ef-412c-952b-f280424272e1";

        /**
         * @covers AuthorizationService::authorizationUrl()
         */
        public function testGivenValidApplicationIdWhenCreatingAuthorizationUrlThenTheUrlShouldBeFineDuringManualTesting()
        {
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationUrlResult = $authorizationService->authorizationUrl(self::APPLICATION_ID);

            $this->assertEquals("https://agrirouter-qa.cfapps.eu10.hana.ondemand.com/application/16b1c3ab-55ef-412c-952b-f280424272e1/authorize?response_type=onboard&state={$authorizationUrlResult->getState()}",
                $authorizationUrlResult->getAuthorizationUrl());
        }

        /**
         * @covers AuthorizationService::authorizationUrlWithRedirect()
         */
        public function testGivenValidApplicationIdAndRedirectUriWhenCreatingAuthorizationUrlThenTheUrlShouldBeFineDuringManualTesting()
        {
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationUrlResult = $authorizationService->authorizationUrlWithRedirect(self::APPLICATION_ID, "https://www.saschadoemer.de");

            $this->assertEquals("https://agrirouter-qa.cfapps.eu10.hana.ondemand.com/application/16b1c3ab-55ef-412c-952b-f280424272e1/authorize?response_type=onboard&state={$authorizationUrlResult->getState()}&redirect_uri=https://www.saschadoemer.de",
                $authorizationUrlResult->getAuthorizationUrl());
        }

        /**
         * @covers AuthorizationService::parseAuthorizationResult()
         */
        public function testGivenValidResponseWhenParsingTheResultThenTheAuthorizationRequestObjectWShouldBeFilled()
        {
            $input =
                "state=6eab2086-0ef2-4b64-94b0-2ce620e66ece&token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6IjI2NGQwNjgzYzkiLCJleHBpcmVzIjoiMjAyMC0wMS0xNFQxMDowOTo1OS4zMTlaIn0%3D&signature=AJOFQmO4Y%2FT8DlNOcTAfpymMFiZQBpJHr4%2FUOfrHuGpzst6UA4kQraJYJtUEKSeEaQ%2FHCf4rJlUcK14ygyGAUtGkca1Y1sUAC1lVggVnECFMnVQAyTQzSnd1DEXjqI8n4Ud4LujSF6oSbiK0DWg1U8U9swwAEQ73Z0SDna7M3OEirY8zPUhGFcRij%2FrJOEFujq2rW%2Bs267z1pnp6FNq%2BoK5nbPBuH0hvCZ57Fz3HI1VadyE77o6rOAZ1HXniGqCGr%2F6v4TqAQ22MY9xhMAfUihtwQ3VLtdHsGSu1OH%2Fs71IQczOzBgeIlMAl4mchRo3l16qSU4k4awufLq7LzDSf5Q%3D%3D";
            $authorizationResult = (new AuthorizationService($this->getEnvironment()))->parseAuthorizationResult($input);
            $this->assertNotNull($authorizationResult->getState());
            $this->assertNotNull($authorizationResult->getToken());
            $this->assertNotNull($authorizationResult->getSignature());
            $this->assertNull($authorizationResult->getError());
        }

        /**
         * @covers AuthorizationService::parseAuthorizationToken()
         */
        public function testGivenValidResponseWhenParsingTheTokenThenTheAuthorizationTokenObjectWShouldBeFilled()
        {
            $input = "state=6eab2086-0ef2-4b64-94b0-2ce620e66ece&token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6IjI2NGQwNjgzYzkiLCJleHBpcmVzIjoiMjAyMC0wMS0xNFQxMDowOTo1OS4zMTlaIn0%3D&signature=AJOFQmO4Y%2FT8DlNOcTAfpymMFiZQBpJHr4%2FUOfrHuGpzst6UA4kQraJYJtUEKSeEaQ%2FHCf4rJlUcK14ygyGAUtGkca1Y1sUAC1lVggVnECFMnVQAyTQzSnd1DEXjqI8n4Ud4LujSF6oSbiK0DWg1U8U9swwAEQ73Z0SDna7M3OEirY8zPUhGFcRij%2FrJOEFujq2rW%2Bs267z1pnp6FNq%2BoK5nbPBuH0hvCZ57Fz3HI1VadyE77o6rOAZ1HXniGqCGr%2F6v4TqAQ22MY9xhMAfUihtwQ3VLtdHsGSu1OH%2Fs71IQczOzBgeIlMAl4mchRo3l16qSU4k4awufLq7LzDSf5Q%3D%3D";

            $authorizationService = new AuthorizationService($this->getEnvironment());

            $authorizationResult = $authorizationService->parseAuthorizationResult($input);
            $this->assertNotNull($authorizationResult->getState());
            $this->assertNotNull($authorizationResult->getToken());
            $this->assertNotNull($authorizationResult->getSignature());
            $this->assertNull($authorizationResult->getError());

            $authorizationToken = $authorizationService->parseAuthorizationToken($authorizationResult);
            $this->assertNotNull($authorizationToken->getRegistrationCode());
            $this->assertNotNull($authorizationToken->getAccount());
            $this->assertNotNull($authorizationToken->getExpires());
        }

        /**
         * @covers AuthorizationService::parseAuthorizationResult()
         */
        public function testGivenInvalidResponseWithToLessParametersThenArgumentErrorShouldBeThrown()
        {
            $this->expectException(AuthorizationException::class);
            $input = "token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6IjI2NGQwNjgzYzkiLCJleHBpcmVzIjoiMjAyMC0wMS0xNFQxMDowOTo1OS4zMTlaIn0%3D";
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationService->parseAuthorizationResult($input);
        }

        /**
         * @covers AuthorizationService::parseAuthorizationResult()
         */
        public function testGivenInvalidResponseWithToManyParametersThenArgumentErrorShouldBeThrown()
        {
            $this->expectException(AuthorizationException::class);
            $input = "test=something&state=6eab2086-0ef2-4b64-94b0-2ce620e66ece&token=eyJhY2NvdW50IjoiNWQ0N2E1MzctOTQ1NS00MTBkLWFhNmQtZmJkNjlhNWNmOTkwIiwicmVnY29kZSI6IjI2NGQwNjgzYzkiLCJleHBpcmVzIjoiMjAyMC0wMS0xNFQxMDowOTo1OS4zMTlaIn0%3D&signature=AJOFQmO4Y%2FT8DlNOcTAfpymMFiZQBpJHr4%2FUOfrHuGpzst6UA4kQraJYJtUEKSeEaQ%2FHCf4rJlUcK14ygyGAUtGkca1Y1sUAC1lVggVnECFMnVQAyTQzSnd1DEXjqI8n4Ud4LujSF6oSbiK0DWg1U8U9swwAEQ73Z0SDna7M3OEirY8zPUhGFcRij%2FrJOEFujq2rW%2Bs267z1pnp6FNq%2BoK5nbPBuH0hvCZ57Fz3HI1VadyE77o6rOAZ1HXniGqCGr%2F6v4TqAQ22MY9xhMAfUihtwQ3VLtdHsGSu1OH%2Fs71IQczOzBgeIlMAl4mchRo3l16qSU4k4awufLq7LzDSf5Q%3D%3D&error=request_declined";
            $authorizationService = new AuthorizationService($this->getEnvironment());
            $authorizationService->parseAuthorizationResult($input);
        }

        /**
         * @covers AuthorizationService::parseAuthorizationToken()
         */
        public function testGivenDeclineConnectionResponseWhenParsingTheTokenThenTheAuthorizationServiceShouldThrowSpecificException()
        {
            $input = "state=5e0492fb-1550-49b9-add7-e480e79f323e&error=request_declined";
            $authorizationResult = (new AuthorizationService($this->getEnvironment()))->parseAuthorizationResult($input);
            $this->assertNotNull($authorizationResult->getState());
            $this->assertNull($authorizationResult->getToken());
            $this->assertNull($authorizationResult->getSignature());
            $this->assertNotNull($authorizationResult->getError());
        }
    }
}