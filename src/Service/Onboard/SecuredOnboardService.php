<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Dto\Requests\OnboardRequest;
    use App\Service\Common\SignatureService;
    use App\Service\Common\UtcDataService;
    use App\Service\Parameters\OnboardParameters;
    use Psr\Http\Message\RequestInterface;

    /**
     * Service for all secured onboard purposes.
     * @package App\Service\Onboard
     */
    class SecuredOnboardService extends AbstractOnboardService
    {
        public function createRequest(?OnboardParameters $onboardParameters, ?string $privateKey = null): RequestInterface
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

            return $this->httpClient->createRequest('POST', $this->environment->onboardUrl(), $headers, $requestBody);
        }
    }
}