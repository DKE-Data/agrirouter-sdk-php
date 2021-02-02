<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Api\Common\HttpClientInterface;
    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Api\Exceptions\SignatureException;
    use App\Dto\Onboard\OnboardResponse;
    use App\Dto\Requests\OnboardRequest;
    use App\Environment\AbstractEnvironment;
    use App\Service\Common\SignatureService;
    use App\Service\Common\UtcDataService;
    use App\Service\Parameters\OnboardParameters;
    use Exception;
    use Psr\Http\Message\RequestInterface;

    /**
     * Abstract Service for all onboard purposes.
     * @package App\Service\Onboard
     */
    abstract class AbstractOnboardService
    {
        protected AbstractEnvironment $environment;
        protected HttpClientInterface $httpClient;

        /**
         * OnboardService constructor.
         * @param AbstractEnvironment $environment The environment to use for the onboard process.
         * @param HttpClientInterface $httpClient The http client used for the onboard process.
         */
        public function __construct(AbstractEnvironment $environment, HttpClientInterface $httpClient)
        {
            $this->environment = $environment;
            $this->httpClient = $httpClient;
        }

        /**
         * Onboard an endpoint using with a prepared request.
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @param string|null $privateKey Null for normal the onboard process | the private key for the secured onboard process.
         * @return OnboardResponse The onboard response from the agrirouter.
         * @throws OnboardException Will be thrown if the onboard process was not successful.
         * @throws Exception Will be thrown in all other cases.
         */
        public abstract function onboard(OnboardParameters $onboardParameters, ?string $privateKey = null): OnboardResponse;

        /**
         * Creates an onboard request using the given parameters.
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @param string $requestUrl The request url for the onboard process.
         * @param string|null $privateKey Null for normal the onboard process | The private key for the secured onboard process.
         * @return RequestInterface The prepared request for the onboard process
         * @throws Exception Will be thrown if the request building was not successful.
         */
        protected function createRequest(OnboardParameters $onboardParameters, string $requestUrl, ?string $privateKey = null): RequestInterface
        {
            $onboardRequest = $this->createOnboardRequest($onboardParameters);
            $requestBody = json_encode($onboardRequest);
            $headers = $this->createRequestHeader($onboardParameters, $requestBody, $privateKey);
            return $this->httpClient->createRequest('POST', $requestUrl, $headers, $requestBody);
        }

        /**
         * Maps the required onboard parameters into the onboard request.
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @return OnboardRequest The prefilled onboard request.
         */
        private function createOnboardRequest(OnboardParameters $onboardParameters): OnboardRequest
        {
            $onboardRequest = new OnboardRequest();
            $onboardRequest->setExternalId($onboardParameters->getUuid());
            $onboardRequest->setApplicationId($onboardParameters->getApplicationId());
            $onboardRequest->setCertificationVersionId($onboardParameters->getCertificationVersionId());
            $onboardRequest->setGatewayId($onboardParameters->getGatewayId());
            $onboardRequest->setCertificateType($onboardParameters->getCertificationType());
            $onboardRequest->setTimeZone(UtcDataService::timeZone($onboardParameters->getOffset()));
            $onboardRequest->setUtcTimestamp(UtcDataService::now());
            return $onboardRequest;
        }

        /**
         * Creates the request header for the onboard request
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @param string $requestBody The json encoded request body.
         * @param string|null $privateKey Null for normal the onboard process | The private key for the secured onboard process.
         * @return array The header array for a request.
         * @throws SignatureException Will be thrown if creation of the agrirouter header signature was not successful.
         */
        private function createRequestHeader(OnboardParameters $onboardParameters, string $requestBody, ?string $privateKey): array
        {
            $headers = [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $onboardParameters->getRegistrationCode()
            ];
            if ($privateKey != null) {
                $headers += [
                    'X-Agrirouter-ApplicationId' => $onboardParameters->getApplicationId(),
                    'X-Agrirouter-Signature' => SignatureService::createXAgrirouterSignature($requestBody, $privateKey)
                ];
            }
            return $headers;
        }

        /**
         * Send the onboard request to the AR.
         * @param RequestInterface $request The request to send, that one differs from the unsecured to secured onboard process.
         * @return OnboardResponse The onboard response from the AR, mapped to the domain object.
         * @throws OnboardException Can be thrown during the onboard process.
         */
        protected function sendRequest(RequestInterface $request): OnboardResponse
        {
            try {
                $response = $this->httpClient->sendAsync($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $onboardResponse = new OnboardResponse();
                $onboardResponse = $onboardResponse->jsonDeserialize($content);
                return $onboardResponse;
            } catch (Exception $exception) {
                if ($exception->getCode() == 400) {
                    throw new OnboardException($exception->getMessage(), ErrorCodes::INVALID_MESSAGE);
                } elseif ($exception->getCode() == 401) {
                    throw new OnboardException($exception->getMessage(), ErrorCodes::BEARER_NOT_FOUND);
                } else {
                    throw new OnboardException($exception->getMessage(), ErrorCodes::UNDEFINED);
                }
            }
        }

    }
}