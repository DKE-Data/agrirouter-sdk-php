<?php declare(strict_types=1);

namespace App\Service\Common;

use App\Api\Common\MessagingService;
use App\Api\Exceptions\ErrorCodes;
use App\Api\Exceptions\OnboardException;
use App\Api\Service\Parameters\MessagingParameters;
use App\Dto\Messaging\Inner\Message;
use App\Dto\Messaging\MessageRequest;
use App\Dto\Messaging\MessagingResult;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;

/**
 * Service to send messages to the AR.
 * @package App\Service\Common
 * @template-implements MessagingService<MessagingParameters>
 */
class HttpMessagingService implements MessagingService
{
    private Client $httpClient;

    /**
     * Constructor.
     * @param Client $httpClient -
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Send message to the AR using the given message parameters.
     * @param MessagingParameters $parameters Messaging parameters.
     * @return MessagingResult -
     * @throws OnboardException Will be thrown in case of an error.
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

        $request = new Request('POST', $parameters->getOnboardResponse()->getConnectionCriteria()->getMeasures(), $headers, $requestBody);

        $promise = $this->httpClient->sendAsync($request,
            [
                RequestOptions::CERT => [CertificateService::createCertificateFile($parameters->getOnboardResponse()), $parameters->getOnboardResponse()->getAuthentication()->getSecret()],
                RequestOptions::SSL_KEY => [CertificateService::createCertificateFile($parameters->getOnboardResponse()), $parameters->getOnboardResponse()->getAuthentication()->getSecret()]
            ])->
        then(function ($response) {
            return (string)$response->getBody();
        }, function ($exception) {
            return $exception;
        });

        $result = $promise->wait();

        if ($result instanceof Exception) {
            if ($result->getCode() == 401) {
                throw new OnboardException($result->getMessage(), ErrorCodes::BEARER_NOT_FOUND);
            } else {
                throw new OnboardException($result->getMessage(), ErrorCodes::UNDEFINED);
            }
        } else {
            return new MessagingResult();
        }
    }
}