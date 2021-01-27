<?php declare(strict_types=1);

namespace App\Service\Onboard {


    use App\Dto\Onboard\AuthorizationResult;
    use App\Dto\Onboard\AuthorizationToken;
    use App\Dto\Onboard\AuthorizationUrlResult;
    use App\Environment\AbstractEnvironment;
    use App\Service\Common\UuidService;
    use ArgumentCountError;

    /**
     * Service for the authorization process.
     * @package App\Service\Onboard
     */
    class AuthorizationService
    {
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
         * Generates the authorization URL for the application used within the onboarding process.
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
         * Generates the authorization URL for the application used within the onboarding process and adds the redirect URI parameter.
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
         */
        public function parseAuthorizationResult(string $authorizationResultUri): AuthorizationResult
        {
            $parameters = explode('&', $authorizationResultUri);
            if (count($parameters) < 2 || count($parameters) > 4) throw new ArgumentCountError("The input '{$authorizationResultUri}' does not meet the specification");

            $authorizationResult = new AuthorizationResult();
            foreach ($parameters as $parameter) {
                $parameterSplit = explode("=", $parameter);
                if (count($parameterSplit) != 2)
                    throw new ArgumentException("Parameter '{parameter}' could not be parsed.");
                $setterName = "set" . ucfirst($parameterSplit[0]);
                $authorizationResult->$setterName($parameterSplit[1]);
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
            $arrayToken = json_decode($decodedToken, true);
            $authorizationToken = $authorizationToken->jsonDeserialize($arrayToken);

            return $authorizationToken;
        }
    }
}