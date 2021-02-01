<?php declare(strict_types=1);

namespace App\Service\Onboard {

    use App\Api\Exceptions\AuthorizationException;
    use App\Api\Exceptions\ErrorCodes;
    use App\Dto\Onboard\AuthorizationResult;
    use App\Dto\Onboard\AuthorizationToken;
    use App\Dto\Onboard\AuthorizationUrlResult;
    use App\Environment\AbstractEnvironment;
    use App\Service\Common\UuidService;
    use JsonException;

    /**
     * Service for the authorization process.
     * @package App\Service\Onboard
     */
    class AuthorizationService
    {
        private const STATE = 'state';
        private const SIGNATURE = 'signature';
        private const TOKEN = 'token';
        private const ERROR = 'error';

        private AbstractEnvironment $environment;

        /**
         * AuthorizationService constructor.
         * @param AbstractEnvironment $environment
         */
        public function __construct(AbstractEnvironment $environment)
        {
            $this->environment = $environment;
        }

        /**
         * Generates the authorization URL for the application used within the onboard process.
         * @param string $applicationId The application ID for the authorization.
         * @return AuthorizationUrlResult The prepared authorization url and the application id
         */
        public function authorizationUrl(string $applicationId): AuthorizationUrlResult
        {
            $state = UuidService::newUuid();
            $authorizationUrl = $this->environment->authorizationUrl($applicationId) . '?response_type=onboard&state=' . $state;
            return new AuthorizationUrlResult($authorizationUrl, $state);
        }

        /**
         * Generates the authorization URL for the application used within the onboard process and adds the redirect URI parameter.
         * @param string $applicationId The application ID for the authorization.
         * @param string $redirectUri The redirect URI.
         * @return AuthorizationUrlResult The prepared authorization url and the application id
         */
        public function authorizationUrlWithRedirect(string $applicationId, string $redirectUri): AuthorizationUrlResult
        {
            $state = UuidService::newUuid();
            $authorizationUrl = $this->environment->authorizationUrl($applicationId) . '?response_type=onboard&state=' . $state
                . '&redirect_uri=' . $redirectUri;
            return new AuthorizationUrlResult($authorizationUrl, $state);
        }

        /**
         * Parsing the result which was attached as parameters to the URL.
         * @param string $authorizationResultUri The authorization result uri with the parameters to parse
         * @return AuthorizationResult The parsed authorization parameters
         * @throws AuthorizationException Will be thrown if
         */
        public function parseAuthorizationResult(string $authorizationResultUri): AuthorizationResult
        {
            $parameters = explode('&', $authorizationResultUri);
            if (count($parameters) < 2 || count($parameters) > 4) {
                throw new AuthorizationException("The number authorization result parameters '{$authorizationResultUri}' does not meet the specification", ErrorCodes::AUTHORIZATION_RESULT_PARAMETER_COUNT_ERROR);
            }

            $authorizationResult = new AuthorizationResult();
            foreach ($parameters as $parameterString) {
                $parameter = explode("=", $parameterString);
                if (count($parameter) != 2)
                    throw new AuthorizationException("Parameter without value in '$parameterString'.", ErrorCodes::AUTHORIZATION_PARAMETER_VALUE_MISSING);
                switch ($parameter[0]) {
                    case self::STATE:
                        $authorizationResult->setState($parameter[1]);
                        break;
                    case self::SIGNATURE:
                        $authorizationResult->setSignature($parameter[1]);
                        break;
                    case self::TOKEN:
                        $authorizationResult->setToken($parameter[1]);
                        break;
                    case self::ERROR:
                        $authorizationResult->setError($parameter[1]);
                        break;
                    default:
                        throw new AuthorizationException("Unknown Parameter '$parameter[0]' in Authorization response '$parameterString'.", ErrorCodes::UNKNOWN_AUTHORIZATION_PARAMETER);
                }
            }
            return $authorizationResult;
        }

        /**
         * Parsing the token from the authorization result.
         * @param AuthorizationResult $authorizationResult The AuthorizationResult that contains the token to parse.
         * @return AuthorizationToken The parsed authorization token
         */
        public function parseAuthorizationToken(AuthorizationResult $authorizationResult): AuthorizationToken
        {
            $authorizationResultToken = urldecode($authorizationResult->getToken());
            $decodedToken = base64_decode($authorizationResultToken, true);
            $authorizationToken = new AuthorizationToken();
            try {
                $authorizationToken = $authorizationToken->jsonDeserialize($decodedToken);
            } catch (JsonException $e) {
                throw new AuthorizationException("Could not parse JSON from token.", ErrorCodes::COULD_NOT_PARSE_AUTHORIZATION_TOKEN, $e);
            }
            return $authorizationToken;
        }
    }
}