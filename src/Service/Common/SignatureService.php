<?php declare(strict_types=1);


namespace App\Service\Common {


    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\SignatureException;
    use Exception;

    /**
     * Service to sign and verify the request and response payloads within the agrirouter authorization process.
     * The agrirouter needs the signature as a hexadecimal string.
     * @package App\Service\Common
     */
    class SignatureService
    {
        /**
         * Verifies signed request and response payload with a signature.
         * @param string $requestBody The payload of a request
         * @param string $signature The signature to verify the payload with.
         * @param string $publicKeyString The public key for the verification
         * @return bool Result whether the verification was successful
         * @throws SignatureException Throws exception if creation of signature has failed.
         */
        public static function verifySignature(string $requestBody, string $signature, string $publicKeyString): bool
        {
            $publicKey = openssl_pkey_get_public($publicKeyString);
            $return = openssl_verify($requestBody, $signature, $publicKey, "sha256WithRSAEncryption");

            if ($return == 1) {
                return true;
            } elseif ($return == 0) {
                return false;
            } else {
                throw new SignatureException("Error occurred while verifying the signature.", ErrorCodes::SIGNATURE_VERIFICATION_ERROR);
            }
        }

        /**
         * Creates the agrirouter signature used for signing the requests.
         * @param string $requestBody The request payload.
         * @param string $privateKey The private key.
         * @return string|null The hexadecimal string representation of the signature.
         * @throws SignatureException Throws exception if creation of signature has failed.
         */
        public static function createXAgrirouterSignature(string $requestBody, string $privateKey): ?string
        {
            $sign = self::createSignature($requestBody, $privateKey);

            return bin2hex($sign);
        }

        /**
         * Creates a common signature for the given request.
         * @param string $requestBody The request payload.
         * @param string $privateKeyString The private key.
         * @return string|null The signed signature.
         * @throws SignatureException Throws exception if creation of signature has failed.
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