<?php declare(strict_types=1);

namespace App\Service\Common {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OutboxException;
    use App\Api\Messaging\HttpClientInterface;
    use App\Api\Messaging\MessagingServiceInterface;
    use App\Api\Service\Parameters\MessagingParameters;
    use App\Dto\Messaging\Inner\Message;
    use App\Dto\Messaging\MessagingResult;
    use App\Dto\Requests\MessageRequest;
    use Exception;

    /**
     * Service to send messages to the AR.
     * @package App\Service\Common
     * @template-implements MessagingServiceInterface<MessagingParameters>
     */
    class HttpMessagingService implements MessagingServiceInterface
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
         * Send message to the AR using the given message parameters.
         * @param MessagingParameters $parameters Messaging parameters.
         * @return MessagingResult -
         * @throws OutboxException Will be thrown in case of an error.
         */
        public function send($parameters): MessagingResult
        {
            $messageRequest = new MessageRequest();
            $messageRequest->setSensorAlternateId($parameters->getOnboardResponse()->getSensorAlternateId());
            $messageRequest->setCapabilityAlternateId($parameters->getOnboardResponse()->getCapabilityAlternateId());

            $messages = [];
            foreach ($parameters->getEncodedMessages() as $encodedMessage) {
                $message = new Message();
                $message->setContent($encodedMessage);
                $message->setTimestamp(UtcDataService::nowAsUnixTimestamp());
                array_push($messages, $message);
            }
            $messageRequest->setMessages($messages);

            $requestBody = json_encode($messageRequest);
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