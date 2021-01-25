<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Dto\Onboard\OnboardResponse;
    use App\Dto\Requests\OnboardRequest;
    use App\Environment\AbstractEnvironment;
    use App\Service\Common\UtcDataService;
    use App\Service\Parameters\OnboardParameters;
    use Exception;
    use GuzzleHttp\Client;
    use GuzzleHttp\Psr7\Request;

    /**
     * Service for all onboard purposes.
     * @package App\Service\Onboard
     */
    class OnboardService
    {
        private AbstractEnvironment $environment;
        private Client $httpClient;
        private UtcDataService $utcDataService;

        /**
         * OnboardService constructor.
         * @param AbstractEnvironment $environment
         * @param UtcDataService $utcDataService
         * @param Client $httpClient
         */
        public function __construct(AbstractEnvironment $environment, UtcDataService $utcDataService, Client $httpClient)
        {
            $this->environment = $environment;
            $this->httpClient = $httpClient;
            $this->utcDataService = $utcDataService;
        }

        /**
         * Onboard an endpoint using the simple onboard procedure and the given parameters.
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @return OnboardResponse|null
         * @throws OnboardException Will be thrown if the onboard was not successful.
         */
        public function onboard(OnboardParameters $onboardParameters): ?OnboardResponse
        {
            $onboardRequest = new OnboardRequest();
            $onboardRequest->setExternalId($onboardParameters->getUuid());
            $onboardRequest->setApplicationId($onboardParameters->getApplicationId());
            $onboardRequest->setCertificationVersionId($onboardParameters->getCertificationVersionId());
            $onboardRequest->setGatewayId($onboardParameters->getGatewayId());
            $onboardRequest->setCertificateType($onboardParameters->getCertificationType());
            $onboardRequest->setTimezone(UtcDataService::timeZone($onboardParameters->getOffset()));
            $onboardRequest->setUtcTimestamp(UtcDataService::now());

            $requestBody = json_encode($onboardRequest);
            $headers = [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $onboardParameters->getRegistrationCode(),
            ];

            $request = new Request('POST', $this->environment->onboardUrl(), $headers, $requestBody);
            $promise = $this->httpClient->sendAsync($request)->
            then(function ($response) {
                return (string)$response->getBody();
            }, function ($exception) {
                return $exception;
            });

            $result = $promise->wait();

            if ($result instanceof Exception) {
                if ($result->getCode() == 401) {
                    throw new OnboardException($result->getMessage(), ErrorCodes::BEARER_NOT_FOUND);
                }else{
                    throw new OnboardException($result->getMessage(), ErrorCodes::UNDEFINED);
                }
            } else {
                $object = json_decode($result, true);
                $onboardResponse = new OnboardResponse();
                $onboardResponse = $onboardResponse->jsonDeserialize($object);
                return $onboardResponse;
            }
        }
    }
}