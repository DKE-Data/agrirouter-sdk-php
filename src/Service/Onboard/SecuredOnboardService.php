<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Dto\Onboard\OnboardResponse;
    use App\Dto\Onboard\VerificationResponse;
    use App\Dto\Requests\OnboardRequest;
    use App\Service\Common\SignatureService;
    use App\Service\Common\UtcDataService;
    use App\Service\Parameters\OnboardParameters;
    use Exception;
    use Psr\Http\Message\RequestInterface;

    /**
     * Service for all secured onboard purposes.
     * @package App\Service\Onboard
     */
    class SecuredOnboardService extends AbstractOnboardService
    {
        public function onboard(OnboardParameters $onboardParameters, ?string $privateKey = null): OnboardResponse
        {
            $request = $this->createRequest($onboardParameters, $this->environment->securedOnboardUrl(), $privateKey);
            try {
                $response = $this->httpClient->sendAsync($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $onboardResponse = new OnboardResponse();
                $onboardResponse = $onboardResponse->jsonDeserialize($content);
                return $onboardResponse;
            } catch (Exception $exception) {
                $this->handleOnboardRequestException($exception);
            }
        }

        public function createRequest(OnboardParameters $onboardParameters, string $requestUrl, ?string $privateKey = null): RequestInterface
        {
            $onboardRequest = new OnboardRequest();
            $onboardRequest->setExternalId($onboardParameters->getUuid());
            $onboardRequest->setApplicationId($onboardParameters->getApplicationId());
            $onboardRequest->setCertificationVersionId($onboardParameters->getCertificationVersionId());
            $onboardRequest->setGatewayId($onboardParameters->getGatewayId());
            $onboardRequest->setCertificateType($onboardParameters->getCertificationType());
            $onboardRequest->setTimeZone(UtcDataService::timeZone($onboardParameters->getOffset()));
            $onboardRequest->setUtcTimestamp(UtcDataService::now());

            $requestBody = json_encode($onboardRequest);
            $headers = [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $onboardParameters->getRegistrationCode(),
                'X-Agrirouter-ApplicationId' => $onboardParameters->getApplicationId(),
                'X-Agrirouter-Signature' => SignatureService::createXAgrirouterSignature($requestBody, $privateKey)
            ];

            return $this->httpClient->createRequest('POST', $requestUrl, $headers, $requestBody);
        }

        public function verify(OnboardParameters $onboardParameters, ?string $privateKey = null): VerificationResponse
        {
            $request = $this->createRequest($onboardParameters, $this->environment->verificationUrl(), $privateKey);
            try {
                $response = $this->httpClient->sendAsync($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $verificationResponse = new VerificationResponse();
                $verificationResponse = $verificationResponse->jsonDeserialize($content);
                return $verificationResponse;
            } catch (Exception $exception) {
                $this->handleOnboardRequestException($exception);
            }
        }
    }
}