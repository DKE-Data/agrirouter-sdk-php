<?php


namespace App\Service\Onboard;


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

abstract class AbstractOnboardService
{
    protected AbstractEnvironment $environment;
    protected Client $httpClient;
    protected UtcDataService $utcDataService;

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
     * @param OnboardParameters|null $onboardParameters
     * @param string|null $privateKey
     * @return OnboardRequest|null
     * @throws Exception
     */
    public abstract function createRequest(?OnboardParameters $onboardParameters, ?string $privateKey = null):?Request;

    /**
     * Onboard an endpoint using the simple onboard procedure and the given parameters.
     * @param OnboardParameters $onboardParameters The onboard parameters.
     * @return OnboardResponse|null
     * @throws OnboardException Will be thrown if the onboarding was not successful.
     */
    public function onboard(?OnboardParameters $onboardParameters, ?string $privateKey = null): ?OnboardResponse
    {
        $request = $this->createRequest($onboardParameters,$privateKey);

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
            } else {
                throw new OnboardException($result->getMessage(), ErrorCodes::UNDEFINED);
            }
        } else {
            $object = json_decode($result, true);
            $onboardingResponse = new OnboardResponse();
            $onboardingResponse = $onboardingResponse->jsonDeserialize($object);
            return $onboardingResponse;
        }
    }
}

