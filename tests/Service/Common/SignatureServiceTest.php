<?php

namespace Lib\Tests\Service\Common {

    use App\Api\Exceptions\ErrorCodes;
    use App\Api\Exceptions\SignatureException;
    use App\Service\Common\SignatureService;
    use PHPUnit\Framework\TestCase;

    /**
     * Class SignatureServiceTest
     * @package Lib\Tests\Service\Common
     */
    class SignatureServiceTest extends TestCase
    {
        private const PRIVATE_KEY = "-----BEGIN PRIVATE KEY-----\n" .
        "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQD85DBkLGppK9Cl\n" .
        "GCQzZYGer8Gs3IyX14IvPKxJgKGBksuX2XnNscgjL13ems69xRtpJ3SabmxgJ2q/\n" .
        "L/gd2fxLu4oezy9QPv9QRTR3UVBJ9fICBNOYCaAQvfQ0R+bWzUyO+q2c6m5MPgfF\n" .
        "PxjkFwoLz5x+kp0dI3LHJet/AxtRt99Xrp60RxoDu9I4gOxagB3W8ifO+MO6TPsi\n" .
        "xyvxlC7u6zHZnh/QspcMRNjWY3n+RDqvQlXNRB26oym7H/fyzVJiwPYBeOzlRLix\n" .
        "/5lFbm0DIuXajYrCX5+k6UdS+wTLOw5Y6LcirQ0HA+DDn6WOwI7yPf/rLd6pRGk2\n" .
        "gY0dAgU/AgMBAAECggEABTemOElxtd5odMaQWN6FKGcqhNYVTy23brfgs9Zy67zQ\n" .
        "1yC53566JTq/FtQeY/tAFm8Wg0NdxY8iJ6FxZlT4PndkTl5bfGLXZiaV8zIAiPaQ\n" .
        "7qac5lohbQFaBOCT7/GtXMMXCr7e1bycWl77Uvt479oXSNTeSZccMT9VS33L4zpK\n" .
        "rYAdHDNs7RqnKul8cyrv4aWjylfqYcUpFVJi6ZlzZnUE1A4ArIJneJUYfOU2LiJD\n" .
        "7BragrBAJzAomIP2mt0GqMA2Kh/j8mRYp0WcmHjvTtmbex0xeuRNfjuAiQ8lUMzn\n" .
        "Gf6OsAHDnacB+KcKr7CbBjKur3xjjcVNO521wU2bQQKBgQD/VSfDDb/3lV2FMcug\n" .
        "nT2YryTmlw4908ODuzT0fSj5rKl4THEq3GXbwLpmt94nYvvBLN4gwFUepOgHkjAZ\n" .
        "DnQRrEVUdHDqQJhf0jf6dett0PEU1uSp+Kj1W2FxH22x+9lNVCgqqT6ljEnPVvBa\n" .
        "40g/AhOLd5/MBPUhHemmzjFZfwKBgQD9jWZ14jk0NNszV5CaD+pll7wZJLnry52D\n" .
        "hRjDSchbMZf92s1hzc+EVCYIq+afNLH2rKOm9Nrmqp5mlsIBj/JMp8t5jF/6JcjM\n" .
        "4yjvyrSpT7Pd/IsUJh+HiB5TUEN2F7TE1JG7PU8t7Pq0AbOQyU7BwW7VRVZPqsqM\n" .
        "4T7V5PO0QQKBgQCquxghVxpbxOaJ36gXN80uQT7daXg+Y1FCznU0XlR9zrRrGkI5\n" .
        "tLHvZUm+0YecspVAsG2/XJwOJ5p3JYs/1ehKwPSC6nFuUc8Rk5GWyi6oeaQamS7B\n" .
        "cOIZXgckCy3ga1T8Xh+VlyGqtMnN+IYzX/K5HvxOr6iMVWxLvqKzxQA+pQKBgGZn\n" .
        "hptT6/Cm7GU3F4LxrKStfN3W1HRkf1CQH+k30oDqbc2sYkj/G7IBXn8gFnv0h2/u\n" .
        "WAZlXEfPXzxl5SNGZQEKwAZuaJEaU5hUosL+Zqr/MtEUX4Oaxh8yHbVedHCssGjS\n" .
        "xa0O3PzaeoLbMQ/oDjP5EO94Gp0AiOAPVRaEat1BAoGAR1od3j3VlTI11jqhVBZ4\n" .
        "/IcHPvexXzTcjdp9kvU0VerhPVMYeFi3kGf5T/yJJKnge5UbPBI+KQHmS9H9qSyL\n" .
        "X6PdDeOLLCyX2Gxqcs2TPzmEICJiduKq9vgUwCeqjqxMi+XL/hazULAFrCoWHem+\n" .
        "ky5DYh0syScvc4aoiJdXXXQ=\n" .
        "-----END PRIVATE KEY-----";

        private const PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----\n" .
        "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA/OQwZCxqaSvQpRgkM2WB\n" .
        "nq/BrNyMl9eCLzysSYChgZLLl9l5zbHIIy9d3prOvcUbaSd0mm5sYCdqvy/4Hdn8\n" .
        "S7uKHs8vUD7/UEU0d1FQSfXyAgTTmAmgEL30NEfm1s1MjvqtnOpuTD4HxT8Y5BcK\n" .
        "C8+cfpKdHSNyxyXrfwMbUbffV66etEcaA7vSOIDsWoAd1vInzvjDukz7Iscr8ZQu\n" .
        "7usx2Z4f0LKXDETY1mN5/kQ6r0JVzUQduqMpux/38s1SYsD2AXjs5US4sf+ZRW5t\n" .
        "AyLl2o2Kwl+fpOlHUvsEyzsOWOi3Iq0NBwPgw5+ljsCO8j3/6y3eqURpNoGNHQIF\n" .
        "PwIDAQAB\n" .
        "-----END PUBLIC KEY-----";


        /**
         * @covers SignatureService::buildSignature
         */
        public function testGivenInvalidCertificatesWhenSigningThenThereShouldBeAnException()
        {
            self::expectException(SignatureException::class);
            self::expectExceptionCode(ErrorCodes::INVALID_SIGNATURE);
            self::expectExceptionMessage("Could not create signature.");
            SignatureService::createSignature("REQUEST CONTENT", substr(self::PRIVATE_KEY, 0, 41));
        }

        /**
         * @covers SignatureService::verifySignature
         */
        public function testGivenInvalidCertificatesWhenVerifyingTheCreatedSignatureThenThereShouldBeAnException()
        {
            self::expectException(SignatureException::class);
            self::expectExceptionCode(ErrorCodes::SIGNATURE_VERIFICATION_ERROR);
            $signature = SignatureService::createSignature("REQUEST CONTENT", self::PRIVATE_KEY);
            SignatureService::verifySignature("REQUEST CONTENT", $signature, substr(self::PRIVATE_KEY, 0, 41));
        }

        /**
         * @covers SignatureService::createXAgrirouterSignature
         */
        public function testGivenValidCertificatesWhenCreatingTheXAgrirouterSignatureThenTheCreatedSignatureShouldBeOk()
        {
            $signature = SignatureService::createXAgrirouterSignature("REQUEST CONTENT", self::PRIVATE_KEY);
            $this->assertNotEmpty($signature);
        }

        /**
         * @covers SignatureService::verifySignature
         */
        public function testGivenValidCertificatesWhenVerifyingTheCreatedSignatureThenTheResultShouldBeOk()
        {
            $signature = SignatureService::createSignature("REQUEST CONTENT", self::PRIVATE_KEY);
            $this->assertTrue(SignatureService::verifySignature("REQUEST CONTENT", $signature, self::PUBLIC_KEY));
        }
    }
}