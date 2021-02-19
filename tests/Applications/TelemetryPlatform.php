<?php declare(strict_types=1);


namespace Lib\Tests\Applications;


class TelemetryPlatform
{
    /**
     * Telemetry Platform for integration testing.
     * @package Lib\Tests\Applications
     */
    public static function privateKey(): string
    {
        return "-----BEGIN PRIVATE KEY-----\n"
            . "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCkAWrcN0FmbWZ+\n"
            . "24qE6maNlf46EjH/BOI5f79wFRJAeG6RXrl8KwWdlfeZOlEYRyq+o4AVuP7Mce6R\n"
            . "x5PR/AEtn1K4Twr5h1+uG+O5/D2jJ0O/+nFnNcX/WgJfEbriDK9ewV8mEnFgG3n5\n"
            . "y6P9RCcu0zMFV9PrKTop419krg2Ij0/to5LLU0DaQXmgvSC7Crx0k9dIhDeZ4jc3\n"
            . "rv4SWzOcZT5vPFtA/5G66ZeeTVX+3iJbasWOdatMc7/cGYxUlnzeNSoCAqmyuHzO\n"
            . "PKHWcjyVwPXWG64TSzkb+QypynUIZaAy+P8uawD0BivsmFSf0k4pGl8wSHhE2pN8\n"
            . "OIIyud/lAgMBAAECgf93UZCtJoYuPumS4aaljOOPntCW7yXwo1zy+D4PDUV6IiRP\n"
            . "HttTuvka7UB1+jeFskEm8Uz+gNjfZQRsiwbAftdcmc1Uyizx3ct+oEvNw/YT/T0N\n"
            . "LufSbwzd+l4/TGbqjtAH4CeZS1Gw2kyjr8RfPnZDWuSDvqIvNI9cak+8r+SotHEL\n"
            . "i1yAZTDvZAqLhbDLbW9Ephxd5QkKfwegybSwxJJcOw+KLPsq0Ibb2YGbkPTL1P6o\n"
            . "Ul3/tiyzEMRc2ufyDJgYCAUmvVBjkbHT7hQNkTL8EPAEXfL+vhte8A98aFPnWMhS\n"
            . "yiJo3T643fjZ8fzPcv8GWmT+z6IxtlXguC/OmQECgYEAzUbQF5YqJ61n6sQiJ0VQ\n"
            . "ZMDDVHzf/oNUCPee4tCVS0ShQ2JURUIbss+lUWbKDfFTXW9mte4VRk+WUh2gQqm6\n"
            . "49IdIpRGx6m6/ye2g6EmXRzeSN9GJu/wltS2uQ+8fmOkWiBUFg8bkLWTTxAY1w0n\n"
            . "31AYcE8wwLb2FXt6Q2u4OQECgYEAzIfrUejd7GS/eaUx2GFKsXaI3B3CmLQ8TQFu\n"
            . "JOh3i2NgcDK5Uwhy5TWozEn4BpwX8UOMciZIGjMr0i5TzpSVca1sv1wyIyWr7+AU\n"
            . "2dmxPTXbkQQpEy7GDE1iCMvsi1lC6F+EUwdCKw6H5oa0oU1XMr/JWJSGh8iFRH5v\n"
            . "H3ic4uUCgYEAycZYV0boqtWddrtRou5UBqUfmxWgC4nFeYcE64gBp9mO9jBaCzXf\n"
            . "ChVnN6tk4u2adxZAjIW47cLfTFoIpF438SYgM1QyeqIQyCueEKa/kfkFWfX1++IP\n"
            . "yALQlPmt20JQU7LBVGmHO+fvI2D8Oa6ZybzuOL7ueg+dtiIIXOsuSwECgYEAjqaZ\n"
            . "1PYymmamOWSjQESTQPTofYVwLOtAR4Eg3jMY9anpIDfwk94HrJ/hTCKnD40dEdPI\n"
            . "B/F1Rni8LorLPwDYvoFzoH/gJC1pHxPN1yeC+6stqZYaQ9vSm8/4+SOppoMqLEI6\n"
            . "W2JrnvYyiPEY2IvFroqfFt34eom7kUsRnASWf5UCgYEAxEMOQ0/+hz1APqCGVo3t\n"
            . "uePUIdsokCusRpWsxUdwHBGqfDjULqVmDXIJuJekV4YUxzyzktRzVms0pgFbr47L\n"
            . "f8IeyJnYFhPn1hgthGlgV7JvFq2TU+EgZYVpVGL6jrw8t78Y4IresceIkz4q/bGV\n"
            . "j35jwp22eLOc0FdPo0KgO4s=\n"
            . "-----END PRIVATE KEY-----\n";
    }

    public static function publicKey(): string
    {
        return "-----BEGIN PUBLIC KEY-----\n"
            . "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApAFq3DdBZm1mftuKhOpm\n"
            . "jZX+OhIx/wTiOX+/cBUSQHhukV65fCsFnZX3mTpRGEcqvqOAFbj+zHHukceT0fwB\n"
            . "LZ9SuE8K+Ydfrhvjufw9oydDv/pxZzXF/1oCXxG64gyvXsFfJhJxYBt5+cuj/UQn\n"
            . "LtMzBVfT6yk6KeNfZK4NiI9P7aOSy1NA2kF5oL0guwq8dJPXSIQ3meI3N67+Elsz\n"
            . "nGU+bzxbQP+RuumXnk1V/t4iW2rFjnWrTHO/3BmMVJZ83jUqAgKpsrh8zjyh1nI8\n"
            . "lcD11huuE0s5G/kMqcp1CGWgMvj/LmsA9AYr7JhUn9JOKRpfMEh4RNqTfDiCMrnf\n"
            . "5QIDAQAB\n"
            . "-----END PUBLIC KEY-----\n";
    }

    public static function applicationId(): string
    {
        return "3c3559c9-7062-4628-a4f7-c9f5aa07265f";
    }

    public static function certificationVersionId(): string
    {
        return "5e488554-d6bb-400c-9632-b3b4b35b07ca";
    }
}