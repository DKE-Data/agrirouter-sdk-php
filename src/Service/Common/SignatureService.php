<?php declare(strict_types=1);


namespace App\Service\Common {


    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\SignatureException;
    use Exception;

    /**
     * Class SignatureService - Service to create the signatures for the authorization process.
     * @package App\Service\Common
     */
    class SignatureService
    {
        /**
         * Verifies a common signature for the given request.
         * @param string $requestBody
         * @param $signature
         * @param string $publicKeyString
         * @return bool
         * @throws SignatureException
         */
        public static function verifySignature(string $requestBody, $signature, string $publicKeyString): bool
        {
            $publicKey = openssl_pkey_get_public($publicKeyString);
            $return = openssl_verify($requestBody, $signature, $publicKey, "sha256WithRSAEncryption");

            if ($return == 1) {
                return true;
            } elseif ($return == 0) {
                return false;
            } else {
                throw new SignatureException("Could not verify signature.", ErrorCodes::INVALID_SIGNATURE);
            }
        }

        /**
         * creates the agrirouter signature used for signing the requests.
         * @param string $requestBody
         * @param string $privateKey
         * @return string|null
         * @throws SignatureException
         */
        public static function createXAgrirouterSignature(string $requestBody, string $privateKey): ?string
        {
            $sign = self::createSignature($requestBody, $privateKey);

            return bin2hex($sign);
        }

        /**
         * Creates a common signature for the given request.
         * @param string $requestBody
         * @param string $privateKeyString
         * @return string|null
         * @throws SignatureException
         */
        public static function createSignature(string $requestBody, string $privateKeyString): ?string
        {
            try {
                $privateKey = openssl_pkey_get_private($privateKeyString);
                $signature = null;
                openssl_sign($requestBody, $signature, $privateKey, OPENSSL_ALGO_SHA256);

                return $signature;
            } catch (Exception $exception) {
                throw new SignatureException("Could not create signature.", ErrorCodes::INVALID_SIGNATURE, $exception);
            }
        }
    }
}