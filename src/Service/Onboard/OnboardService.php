<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Dto\Requests\OnboardRequest;
    use App\Service\Common\UtcDataService;
    use App\Service\Parameters\OnboardParameters;
    use Psr\Http\Message\RequestInterface;

    /**
     * Service for all unsecured onboard purposes.
     * @package App\Service\Onboard
     */
    class OnboardService extends AbstractOnboardService
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
            $onboardRequest->setUTCTimestamp(UtcDataService::now());

            $requestBody = json_encode($onboardRequest);
            $headers = [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $onboardParameters->getRegistrationCode(),
            ];

            return $this->httpClient->createRequest('POST', $this->environment->onboardUrl(), $headers, $requestBody);
        }
    }
}