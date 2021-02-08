<?php declare(strict_types=1);


namespace Lib\Tests\Applications;

/**
 * Farming Software for integration testing.
 * @package Lib\Tests\Applications
 */
class FarmingSoftware
{
    public static function privateKey(): string
    {
        return "-----BEGIN PRIVATE KEY-----\n"
            . "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDCiSNJmpAbrqF5\n"
            . "wOe4kPtavH50OxfjevZjgFhoG/lO+nkJz366655pjKqkwmlxF3GhoXMppAR2K+Dh\n"
            . "x3g46dJGA/dG5Ju5TpLnPn3AskHJMcagGE+0Lb29pXUX7ZAdO0MrqIVSh9UZ3j2v\n"
            . "+MlF4GOwUkOLPu8IgtKK13vlTu3qYKmFzSss9M3r/t7hZemGyGUq/GVZ8wvYWFTc\n"
            . "SdBhOZBzGDpQmRKiL5wcF1HyZfmT8mwbPITzf9Pd+9cDvQT74Ohmu920wwQavS+n\n"
            . "hPaOFEW9io9gIsKY3y81Qt4OR+xQ20BdmsRscWwWaSoQpjol0+f3OnTFmLMGvjuw\n"
            . "y8keLp+zAgMBAAECggEAGm87MdHEelaBD8PkWQKufTZ28oFjLiCz4XJ70O3YM2C6\n"
            . "2NfuzyScWphoSco3PMxkPwPq28IzbwFoZhXOYuF3FteMHwCKnqQsNysZOHmgkd/n\n"
            . "LzMOhZvfmCknH7DC8A3xAzo7n6rgjMrT1Bk49HtY4IlVL1NGdQAd7wr4BYFzt/Dc\n"
            . "8qXnqXWqWcvgktDQBKEPXc8XAQom6fWFcnx+CALUIx3c4QXaCgdG/EuWeaqqM14j\n"
            . "2tM1PwVh3rxiDiwMJSDEQVSxkJtM9M+EEC6oCvBPE4txjaHBjQqsxZJ9po5Va00+\n"
            . "Wm6DuPEfkKTxX1J0a/le8SA0JZ+Tlz/VXOySaeMKYQKBgQDpUoPOfpx/FfOIr4Tk\n"
            . "311zqcPfY+XiCwq56CTfVYERF8QSDQAMuuW3K53SFLTwjgUEq7BwFshhhcTdRxsh\n"
            . "QtKAHg4NpownmGnmQCG681eFTB4IiiGNMlEt6hXh3MQK4QfjDFL/0Y1tXStsM7xB\n"
            . "ouwPOZweWmlv7u7kVEDx4kHEUwKBgQDVcYp2pI/SjXtsFF/LRy/0xW/dgRQMortV\n"
            . "IPYkYvSO/w2dMV2wX1Ly78wFs4uj4ODDfyeVG2xDjn6svQR67FtHPLyBS+SwNTbY\n"
            . "jfzRV1EydHweZgdw9/1yA9zvSPVMvieSgeiR++2YmEEtJYSEoxuBkVSnLAXKFy3/\n"
            . "4RCHrhBLIQKBgB6im/XX//pbyn8u9JcMkPun1bUWK8/zPTRNu9GrK2gwI6lvFYuW\n"
            . "WqUjT/SOjXdsXlJPrLn689KCOugG9xP17yetSpEWRh7Iz1bRItymKN6ysNrUMDWW\n"
            . "3rvVmYvvbboz89InAxrdy+EJM7NgU56WosIZAVum6WMuDyXhvilEWPhLAoGALwnd\n"
            . "tPXhSEqr147J6befhvb4Bz3KGFrIpCMme7BfKyBkdK8LcbIgSq+0K9F8xbnqbssY\n"
            . "AxFPE7hUjGb/lMN//jwRYwFBvd+MXb8050GyAEeRjvV3UFsmvLjDOOzAOpBxkiUV\n"
            . "Bw8ZNpbfTj7FbKGxjyVjHZBjGj1vgsOr6+rdZmECgYEA5gBLtqvsebobo86J4LL7\n"
            . "6VaXtgbgShm8GYMfTDPxkmAv4a89Lpec2UQ6EaVt7xHSdk4hMidfZHGambOtd+Zh\n"
            . "DTWnVSzxt91ZSEcDc3gTxK3hBltTga+9Y63S+/6vVykWKRtAvqkiEEgLNnNXOFNX\n"
            . "lE8ylEsO1whZyzKceV8uaqA=\n"
            . "-----END PRIVATE KEY-----\n";
    }

    public static function publicKey(): string
    {
        return "-----BEGIN PUBLIC KEY-----\n"
            . "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwokjSZqQG66hecDnuJD7\n"
            . "Wrx+dDsX43r2Y4BYaBv5Tvp5Cc9+uuueaYyqpMJpcRdxoaFzKaQEdivg4cd4OOnS\n"
            . "RgP3RuSbuU6S5z59wLJByTHGoBhPtC29vaV1F+2QHTtDK6iFUofVGd49r/jJReBj\n"
            . "sFJDiz7vCILSitd75U7t6mCphc0rLPTN6/7e4WXphshlKvxlWfML2FhU3EnQYTmQ\n"
            . "cxg6UJkSoi+cHBdR8mX5k/JsGzyE83/T3fvXA70E++DoZrvdtMMEGr0vp4T2jhRF\n"
            . "vYqPYCLCmN8vNULeDkfsUNtAXZrEbHFsFmkqEKY6JdPn9zp0xZizBr47sMvJHi6f\n"
            . "swIDAQAB\n"
            . "-----END PUBLIC KEY-----\n";
    }

    public static function applicationId(): string
    {
        return "905152eb-c526-47a3-b871-aa46d065bb4c";
    }

    public static function certificationVersionId(): string
    {
        return "c8ee0fc3-056c-4d81-8eba-fb4f8208c827";
    }

}