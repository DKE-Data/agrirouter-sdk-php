<?php declare(strict_types=1);

namespace App\Service\Common;

use App\Api\Common\MessagingService;
use App\Api\Service\Parameters\MessagingParameters;
use App\Dto\Messaging\Inner\Message;
use App\Dto\Messaging\MessageRequest;
use App\Dto\Messaging\MessagingResult;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

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
     * @param MessagingParameters $parameters Messaging paramters.
     * @return MessagingResult -
     */
    public function send($parameters): MessagingResult
    {
        $messageRequest = new MessageRequest();
        $messageRequest->setSensorAlternateId($parameters->getOnboardResponse()->getSensorAlternateId());
        $messageRequest->setCapabilityAlternateId($parameters->getOnboardResponse()->getCapabilityAlternateId());

        $messages = [];
        foreach ($messageRequest->getMessages() as $encodedMessage) {
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
        $promise = $this->httpClient->sendAsync($request)->
        then(function ($response) {
            return (string)$response->getBody();
        }, function ($exception) {
            return $exception;
        });

        return $promise->wait();
    }
}