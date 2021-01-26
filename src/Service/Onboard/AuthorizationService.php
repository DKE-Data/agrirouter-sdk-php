<?php declare(strict_types=1);

namespace App\Service\Onboard;


use App\Dto\Onboard\AuthorizationResult;
use App\Dto\Onboard\AuthorizationToken;
use App\Dto\Onboard\AuthorizationUrlResult;
use App\Environment\AbstractEnvironment;
use App\Service\Common\UuidService;
use ArgumentCountError;

/**
 * Class AuthorizationService - Service for the authorization process.
 * @package App\Service\Onboard
 */
class AuthorizationService
{
    private AbstractEnvironment $environment;

    public function __construct(AbstractEnvironment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Generates the authorization URL for the application used within the onboarding process.
     * @param string $applicationId
     * @return AuthorizationUrlResult
     */
    public function authorizationUrl(string $applicationId): ?AuthorizationUrlResult
    {
        $state = UuidService::newUuid();
        $authorizationUrl = $this->environment->authorizationUrl($applicationId) . '?response_type=onboard&state=' . $state;
        return new AuthorizationUrlResult($authorizationUrl, $state);
    }

    /**
     * Generates the authorization URL for the application used within the onboarding process and adds the redirect URI parameter.
     * @param string $applicationId
     * @param string $redirectUri
     * @return AuthorizationUrlResult
     */
    public function authorizationUrlWithRedirect(string $applicationId, string $redirectUri): ?AuthorizationUrlResult
    {
        $state = UuidService::newUuid();
        $authorizationUrl = $this->environment->authorizationUrl($applicationId) . '?response_type=onboard&state=' . $state
            . '&redirect_uri=' . $redirectUri;
        return new AuthorizationUrlResult($authorizationUrl, $state);
    }

    /**
     * Parsing the result which was attached as parameters to the URL.
     * @param string $authorizationResult
     * @return AuthorizationResult|null
     */
    public function parseAuthorizationResult(string $authorizationResult): ?AuthorizationResult
    {
        $split = explode('&', $authorizationResult);

        if (count($split) < 2 || count($split) > 4) throw new ArgumentCountError("The input '{authorizationResult}' does not meet the specification");

        $result = new AuthorizationResult();
        foreach ($split as $parameter) {
            $parameterSplit = explode("=", $parameter);
            if (count($parameterSplit) != 2)
                throw new ArgumentException("Parameter '{parameter}' could not be parsed.");
            $setterName = "set" . ucfirst($parameterSplit[0]);
            $result->$setterName($parameterSplit[1]);
        }

        return $result;
    }

    /**
     * Parsing the token from the authorization result.
     * @param AuthorizationResult|null $authorizationResult
     * @return AuthorizationToken|null
     */
    public function parseAuthorizationToken(?AuthorizationResult $authorizationResult): ?AuthorizationToken
    {
        $authorizationResultToken = urldecode($authorizationResult->getToken());
        $decodedToken = base64_decode($authorizationResultToken, true);
        $authorizationToken = new AuthorizationToken();
        $arraytoken = json_decode($decodedToken, true);
        $authorizationToken = $authorizationToken->jsonDeserialize($arraytoken);

        return $authorizationToken;
    }
}