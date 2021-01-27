<?php


namespace Lib\Tests\Service\Common;


use App\Api\Exceptions\ErrorCodes;
use App\Api\Exceptions\SignatureException;
use App\Api\Exceptions\CouldNotVerifySignatureException;
use App\Service\Common\SignatureService;
use PHPUnit\Framework\TestCase;

/**
 * Class SignatureServiceTest
 * @package Lib\Tests\Service\Common
 */
class SignatureServiceTest extends TestCase
{
    private const PRIVATE_KEY = "-----BEGIN PRIVATE KEY-----\n" .
    "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCZ08nZ3MHDsUpG\n" .
    "6Ds9jZfuVq63f9Fo/ewvLldU+2XGzM8km3zUxtWfaIPE68fT9cm5W8E0XI6jCn+G\n" .
    "GCOEpHiHnBizCO9KDjDQGYRyrTZ2DiUg2AcP7tYf5E3HzBvCocVoB2M4HcjmjGfZ\n" .
    "q7Ddf+50T5jDl1L3gSwIeHpbuiH8V+fghsz3RV19finBRdRQ6VTFZ6lSPeOApQuQ\n" .
    "JQeLsjCZC9kAQ9yoL1chsAV0Ua/e0epbKFIZ1amqXMOJWG/RE4f6toQgiLYSbX8t\n" .
    "28P1QtSrRcqIdXW1BV5VhvXlMP6SluvxwTpc7EMZ0vHb3RMoWOUZkXZ0Pj6i3gjD\n" .
    "LH1Ri9ETAgMBAAECggEAM2glLUMKmAjwHykt3wCqNPLP+a/j/jfZjdzpP5OyLBE2\n" .
    "6m9x9LOsIVUusAjxo2Kg8up4czS5UEBKZLg9am8CfqNKV86VFUUPyAO3ERHUwPgy\n" .
    "LTs0hP0WntrPqYULA+zHCWBqpo7BnFZwwDwR47wEpucQ0NCJ3//RhNUqYuwdvnPn\n" .
    "GBIuxuryPz/xk1hoZj2O/Tt85Be2ayg4jlcP0NWz+cJNABs7s9Ud+Iph6nUm3ZXC\n" .
    "c8CRgk/4n9D2umtyfJDvD3U89RURVA4r+gGJfRe25ynWFXfMooT/CMBipNxePOQk\n" .
    "9gP0Mi3OwR6cw6j5Y1u7RFAeNpueFHJYXlwP10fxlQKBgQDWZBuuAEvpRfwLVR78\n" .
    "AxkZL7dTUSCDVXD3ZGG9HEIsFZ3AwYuZATEaqlozsEY8MJrvggH5SRePvAtL+WC2\n" .
    "XEtzV7Qlp6Olft7SPYf5jjDlr+ExQR9RUVd+QGmQR2ckiHurDT7qKLkRWyzizKVd\n" .
    "AeQ2VvI0WDUfdcRFUeivO0+t1QKBgQC3rptBvMkVjzlP3j1NSd50iNTeFs1KWPUL\n" .
    "hUYFa6xwUBcOwT6mxY4pUvYzVU4jUWYyKLVrXGS98AGXgMsvjYKLBui4G+8tMFr4\n" .
    "YCQnRg2OYFhvCzkPOSd44svCKiJwb1Z52wlNt+/5MJd2BCJKmr7IHM0/QruomXSr\n" .
    "KaGptSavRwKBgB5lmzcUe67baV0B+J6qochcZ5W8juuQFbA93028z/UIK5HE31IW\n" .
    "RSwOszNY7txh19XVjQoXPuv2MXPYK8ovU9NKwBQGrMEuN9F/P+Q2MOCgC0G8hgA1\n" .
    "lD4mS9FVDl6zqzUZb64rj/HSB48wjNGfDwhVU4nwZ7fKLsXVUzRlKGrxAoGBAK/M\n" .
    "SYQNL1eXEH9EcN98B5D5NOKqaCj0IvO3xmCBwTRtMuRIR7JN+dXLNR/EUVi0G19+\n" .
    "XsQ2VaUEBEIAXndQtNRi9HDhK3TQhwCY44FDJs5Rn9IHp8DOLt0gE2vt77A0n0tZ\n" .
    "O/pKRT72JsApDLjiih30kuws7nZbCLKY0cUfP7sZAoGBAIUcJgLqjI/b/2X54/cs\n" .
    "SJhLzT/7GcjGW0+o9PZFOzpLkbmUwlPJIZNGKfCl0iXd4yV7HuRQGHkAn+V3vHc2\n" .
    "I/mMtUP/AvINa6afzSexQlodOwycf8WP2YT+jLLBn4trPguMpKhVo1Hg/imcOaY2\n" .
    "jFpgApgO3s0sPrCPerRnven1\n" .
    "-----END PRIVATE KEY-----\n";

     private const PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----\n" .
    "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmdPJ2dzBw7FKRug7PY2X\n" .
    "7laut3/RaP3sLy5XVPtlxszPJJt81MbVn2iDxOvH0/XJuVvBNFyOowp/hhgjhKR4\n" .
    "h5wYswjvSg4w0BmEcq02dg4lINgHD+7WH+RNx8wbwqHFaAdjOB3I5oxn2auw3X/u\n" .
    "dE+Yw5dS94EsCHh6W7oh/Ffn4IbM90VdfX4pwUXUUOlUxWepUj3jgKULkCUHi7Iw\n" .
    "mQvZAEPcqC9XIbAFdFGv3tHqWyhSGdWpqlzDiVhv0ROH+raEIIi2Em1/LdvD9ULU\n" .
    "q0XKiHV1tQVeVYb15TD+kpbr8cE6XOxDGdLx290TKFjlGZF2dD4+ot4Iwyx9UYvR\n" .
    "EwIDAQAB\n" .
    "-----END PUBLIC KEY-----\n";


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
        $this->markTestIncomplete("Certificates invalid?!");
        self::expectException(SignatureException::class);
        self::expectExceptionCode(ErrorCodes::INVALID_SIGNATURE);
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