<?php declare(strict_types=1);


namespace App\Service\Common {


    use App\Exception\CouldNotCreateSignatureException;
    use App\Exception\CouldNotVerifySignatureException;
    use Exception;


    class SignatureService
    {
        public static function verifySignature(string $requestBody, $signature, string $publicKeyString): bool
        {
            $publicKey = openssl_pkey_get_public($publicKeyString);
            $return = openssl_verify($requestBody, $signature, $publicKey, "sha256WithRSAEncryption");

            if ($return == 1) {
                return true;
            } elseif ($return == 0) {
                return false;
            } else {
                throw new CouldNotVerifySignatureException("Could not verify signature.", 666);
            }
        }

        public static function createXAgrirouterSignature(string $requestBody, string $privateKey): ?string
        {
            $sign = self::createSignature($requestBody, $privateKey);

            return bin2hex($sign);
        }

        public static function createSignature(string $requestBody, string $privateKeyString): ?string
        {
            try {
                $privateKey = openssl_pkey_get_private($privateKeyString);
                $signature = null;
                openssl_sign($requestBody, $signature, $privateKey, OPENSSL_ALGO_SHA256);

                return $signature;
            } catch (Exception $exception) {
                throw new CouldNotCreateSignatureException("Could not create signature.", 666, $exception);
            }
        }
    }
}