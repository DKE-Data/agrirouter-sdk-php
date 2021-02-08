<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\RevokeException;
    use App\Api\Exceptions\SignatureException;
    use App\Api\Messaging\HttpClientInterface;
    use App\Dto\Requests\RevokeRequest;
    use App\Environment\AbstractEnvironment;
    use App\Service\Common\SignatureService;
    use App\Service\Common\UtcDataService;
    use App\Service\Parameters\RevokeParameters;
    use Exception;

    /**
     * Service for revoking endpoints.
     * @package App\Service\Onboard
     */
    class RevokeService
    {
        protected AbstractEnvironment $environment;
        protected HttpClientInterface $httpClient;

        /**
         * Constructor.
         * @param AbstractEnvironment $environment The environment to use for the onboard process.
         * @param HttpClientInterface $httpClient The http client used for the onboard process.
         */
        public function __construct(AbstractEnvironment $environment, HttpClientInterface $httpClient)
        {
            $this->environment = $environment;
            $this->httpClient = $httpClient;
        }

        /**
         * Revokes one or more existing endpoints.
         * @param RevokeParameters $revokeParameters The revoke parameters.
         * @param string The private key for the secured revocation process.
         * @throws RevokeException Will be thrown if there are errors during the revocation process.
         * @throws SignatureException Will be thrown if failures occur while the signature for the request is created.
         * @throws Exception Will be thrown in any other error case.
         */
        public function revoke(RevokeParameters $revokeParameters, string $privateKey)
        {
            $revokeRequest = new RevokeRequest();
            $revokeRequest->setAccountId($revokeParameters->getAccountId());
            $revokeRequest->setEndpointIds($revokeParameters->getEndpointIds());
            $revokeRequest->setTimeZone(UtcDataService::timeZone($revokeParameters->getOffset()));
            $revokeRequest->setUtcTimestamp(UtcDataService::now());

            $requestBody = json_encode($revokeRequest);
            $headers = [
                'Content-type' => 'application/json',
                'X-Agrirouter-ApplicationId' => $revokeParameters->getApplicationId(),
                'X-Agrirouter-Signature' => SignatureService::createXAgrirouterSignature($requestBody, $privateKey)
            ];

            $revokeHttpRequest = $this->httpClient->createRequest('DELETE', $this->environment->revokeUrl(), $headers, $requestBody);
            try {
                $this->httpClient->sendRequest($revokeHttpRequest);
            } catch (Exception $exception) {
                if ($exception->getCode() == 400) {
                    throw new RevokeException($exception->getMessage(), ErrorCodes::INVALID_MESSAGE);
                } elseif ($exception->getCode() == 401) {
                    throw new RevokeException($exception->getMessage(), ErrorCodes::AUTHORIZATION_FAILED);
                } else {
                    throw new RevokeException($exception->getMessage(), ErrorCodes::UNDEFINED);
                }
            }
        }
    }
}