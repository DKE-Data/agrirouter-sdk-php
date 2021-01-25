<?php declare(strict_types=1);

namespace App\Service\Onboard;


use App\Dto\Onboard\AuthorizationResult;
use App\Dto\Onboard\AuthorizationToken;
use App\Dto\Onboard\AuthorizationUrlResult;
use App\Environment\AbstractEnvironment;
use App\Service\Common\UuidService;
use ArgumentCountError;

/*
 * Service for the authorization process.
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

    public function parseauthorizationResult(string $authorizationResult): ?AuthorizationResult
    {
        $split = explode('&', $authorizationResult);

        //$parameters = new Dictionary<string, string>();
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

    public function parseAuthorizationToken(?AuthorizationResult $authorizationResult): ?AuthorizationToken
    {
        $authorizationResultToken = urldecode($authorizationResult->getToken());
        $decodedToken = base64_decode($authorizationResultToken,true);
        //$decodedToken = mb_convert_encoding( $authorizationResultToken, "UTF-8", "BASE64" );
        $authorizationToken = new AuthorizationToken();
        $arraytoken = json_decode($decodedToken,true);
        $authorizationToken = $authorizationToken->jsonDeserialize($arraytoken);

        return $authorizationToken;
//            return
//                (AuthorizationToken) JsonConvert.DeserializeObject(
//                    Encoding.UTF8.GetString(Convert.FromBase64String(authorizationResult.Token)),
//                    typeof(AuthorizationToken));
    }
}