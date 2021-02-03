<?php

namespace App\Service\Messaging\Http {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OutboxException;
    use App\Api\Messaging\HttpClientInterface;
    use App\Dto\Messaging\Http\OutboxResponse;
    use App\Dto\Onboard\OnboardResponse;
    use App\Service\Common\CertificateService;
    use Exception;

    /**
     * Service to interact with the outbox.
     * @package App\Service\Messaging\Http
     */
    class OutboxService
    {
        private HttpClientInterface $httpClient;

        /**
         * Constructor.
         * @param HttpClientInterface $httpClient -
         */
        public function __construct(HttpClientInterface $httpClient)
        {
            $this->httpClient = $httpClient;
        }

        /**
         * Poll the outbox for the endpoint and return the content.
         * @param OnboardResponse $onboardResponse The onboard response for the endpoint.
         * @return OutboxResponse The response for the request.
         * @throws OutboxException Will be thrown if there is any error during the polling.
         */
        public function fetch(OnboardResponse $onboardResponse): OutboxResponse
        {
            $headers = [
                'Content-type' => 'application/json',
            ];
            $request = $this->httpClient->createRequest('GET', $onboardResponse->getConnectionCriteria()->getCommands(), $headers);
            try {
                $response = $this->httpClient->sendRequest($request,
                    [
                        'cert' => [CertificateService::createCertificateFile($onboardResponse), $onboardResponse->getAuthentication()->getSecret()],
                        'ssl_key' => [CertificateService::createCertificateFile($onboardResponse), $onboardResponse->getAuthentication()->getSecret()]
                    ]);
                if ($response->getStatusCode() != 200) {
                    throw new OutboxException("Could not fetch messages from outbox. Status code was '" . $response->getStatusCode() . "'", ErrorCodes::COULD_NOT_FETCH_MESSAGES_FROM_OUTBOX);
                } else {
                    $response->getBody()->rewind();
                    $outboxResponse = new OutboxResponse();
                    $outboxResponse->setStatusCode($response->getStatusCode());
                    $outboxResponse->jsonDeserialize($response->getBody()->getContents());
                    return $outboxResponse;
                }
            } catch (Exception $exception) {
                throw new OutboxException($exception->getMessage(), ErrorCodes::UNDEFINED);
            }

        }

    }
}