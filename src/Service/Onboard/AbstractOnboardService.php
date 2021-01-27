<?php declare(strict_types=1);


namespace App\Service\Onboard {


    use App\Api\Common\HttpClient;
    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Dto\Onboard\OnboardResponse;
    use App\Environment\AbstractEnvironment;
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
        protected HttpClient $httpClient;
        protected UtcDataService $utcDataService;

        /**
         * OnboardService constructor.
         * @param AbstractEnvironment $environment The environment to use for onboarding
         * @param UtcDataService $utcDataService The time service for UTC time data
         * @param Client $httpClient The http client used for onboarding
         */
        public function __construct(AbstractEnvironment $environment, UtcDataService $utcDataService, HttpClient $httpClient)
        {
            $this->environment = $environment;
            $this->httpClient = $httpClient;
            $this->utcDataService = $utcDataService;
        }

        /**
         * Onboard an endpoint using with a prepared request.
         * @param OnboardParameters $onboardParameters The onboard parameters.
         * @param string|null $privateKey Null for normal onboarding | the private key for secured onboarding.
         * @return OnboardResponse The onboard response from the agrirouter.
         * @throws OnboardException Will be thrown if the onboarding was not successful.
         */
        public function onboard(OnboardParameters $onboardParameters, ?string $privateKey = null): OnboardResponse
        {
            $request = $this->createRequest($onboardParameters, $privateKey);
            try {
                $response = $this->httpClient->sendAsync($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $arrayResponse = json_decode($content, true);
                $onboardResponse = new OnboardResponse();
                $onboardResponse = $onboardResponse->jsonDeserialize($arrayResponse);
                return $onboardResponse;
            } catch (Exception $exception){
                if ($exception->getCode() == 401) {
                    throw new OnboardException($exception->getMessage(), ErrorCodes::BEARER_NOT_FOUND);
                } else {
                    throw new OnboardException($exception->getMessage(), ErrorCodes::UNDEFINED);
                }
            }
        }

        /**
         * Creates an onboard request using the given parameters.
         * @param OnboardParameters|null $onboardParameters The onboard parameters.
         * @param string|null $privateKey Null for normal onboarding | The private key for secured onboarding.
         * @return Request The prepared request for onboarding
         * @throws Exception Will be thrown if the request building was not successful.
         */
        public abstract function createRequest(?OnboardParameters $onboardParameters, ?string $privateKey = null): RequestInterface;
    }
}