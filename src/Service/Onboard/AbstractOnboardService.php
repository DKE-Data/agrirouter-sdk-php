<?php declare(strict_types=1);


namespace App\Service\Onboard {


    use App\Api\Common\HttpClientInterface;
    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\OnboardException;
    use App\Dto\Onboard\OnboardResponse;
    use App\Environment\AbstractEnvironment;
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
         */
        public function onboard(OnboardParameters $onboardParameters, ?string $privateKey = null): OnboardResponse
        {
            $request = $this->createRequest($onboardParameters, $privateKey);
            try {
                $response = $this->httpClient->sendAsync($request);
                $response->getBody()->rewind();
                $content = $response->getBody()->getContents();
                $onboardResponse = new OnboardResponse();
                $onboardResponse = $onboardResponse->jsonDeserialize($content);
                return $onboardResponse;
            } catch (Exception $exception) {
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
         * @param string|null $privateKey Null for normal the onboard process | The private key for the secured onboard process.
         * @return RequestInterface The prepared request for the onboard process
         * @throws Exception Will be thrown if the request building was not successful.
         */
        public abstract function createRequest(?OnboardParameters $onboardParameters, ?string $privateKey = null): RequestInterface;
    }
}