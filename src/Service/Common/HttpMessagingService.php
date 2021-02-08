<?php declare(strict_types=1);

namespace App\Service\Common {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OutboxException;
    use App\Api\Messaging\HttpClientInterface;
    use App\Api\Service\Parameters\MessagingParameters;
    use App\Dto\Messaging\MessagingResult;
    use Exception;

    /**
     * Service to send messages to the AR via HTTP.
     * @package App\Service\Common
     * @template-implements MessagingServiceInterface<MessagingParameters>
     */
    class HttpMessagingService extends AbstractMessagingService
    {
        private HttpClientInterface $httpClient;

        /**
         * Constructor.
         * @param HttpClientInterface $httpClient .
         */
        public function __construct(HttpClientInterface $httpClient)
        {
            $this->httpClient = $httpClient;
        }

        /**
         * Send message to the AR using the given message parameters.
         * @param MessagingParameters $parameters Messaging parameters.
         * @return MessagingResult .
         * @throws OutboxException Will be thrown in case of an error.
         * @throws Exception Will be thrown in case of an error.
         */
        public function send($parameters): MessagingResult
        {
            $requestBody = json_encode($this->createMessageRequest($parameters));
            $headers = [
                'Content-type' => 'application/json',
            ];

            $request = $this->httpClient->createRequest('POST', $parameters->getOnboardResponse()->getConnectionCriteria()->getMeasures(), $headers, $requestBody);

            try {
                $this->httpClient->sendRequest($request,
                    [
                        'cert' => [CertificateService::createCertificateFile($parameters->getOnboardResponse()), $parameters->getOnboardResponse()->getAuthentication()->getSecret()],
                        'ssl_key' => [CertificateService::createCertificateFile($parameters->getOnboardResponse()), $parameters->getOnboardResponse()->getAuthentication()->getSecret()]
                    ]);
                $messagingResult = new MessagingResult();
                $messageIds = [];
                array_push($messageIds, $parameters->getApplicationMessageId());
                $messagingResult->setMessageIds($messageIds);
                return $messagingResult;
            } catch (Exception $exception) {
                if ($exception->getCode() == 400) {
                    throw new OutboxException($exception->getMessage(), ErrorCodes::INVALID_MESSAGE);
                } else {
                    throw new OutboxException($exception->getMessage(), ErrorCodes::UNDEFINED);
                }
            }
        }
    }
}