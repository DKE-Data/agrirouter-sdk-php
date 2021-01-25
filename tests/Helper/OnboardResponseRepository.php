<?php

namespace Lib\Tests\Helper {

    use App\Dto\Onboard\OnboardResponse;
    use Exception;

    /**
     * Read onboard responses from file located in the test folder.
     * @package Lib\Tests\Helper
     */
    class OnboardResponseRepository
    {
        /**
         * Read and parse a onboard response from the storage.
         * @param string $identifier
         * @return OnboardResponse
         * @throws Exception
         */
        public static function read(string $identifier): OnboardResponse
        {
            match ($identifier) {
                Identifier::COMMUNICATION_UNIT => $data = Content::COMMUNICATION_UNIT,
                default => throw new Exception("Could not read onboard response."),
            };
            $json = json_decode($data, true);
            $onboardResponse = new OnboardResponse();
            return $onboardResponse->jsonDeserialize($json);
        }
    }

    /**
     * Holding all identifiers to access the onboard responses.
     * @package Lib\Tests\Helper
     */
    class Identifier
    {
        public const COMMUNICATION_UNIT = "COMMUNICATION_UNIT";
    }

    class Content
    {
        public const COMMUNICATION_UNIT = "{\"deviceAlternateId\":\"d9f7e895-e1dc-4e0f-80c8-e5df1daea318\",\"capabilityAlternateId\":\"3035ec70-dca1-4d71-a000-e79eb5891f81\",\"sensorAlternateId\":\"1c04474f-53ab-4e87-8feb-71b3dc3b86df\",\"connectionCriteria\": {\"gatewayId\":\"3\",\"measures\":\"https://dke-qa.eu10.cp.iot.sap/iot/gateway/rest/measures/d9f7e895-e1dc-4e0f-80c8-e5df1daea318\",\"commands\":\"https://dke-qa.eu10.cp.iot.sap/iot/gateway/rest/commands/d9f7e895-e1dc-4e0f-80c8-e5df1daea318\" },\"authentication\": {\"type\":\"PEM\",\"secret\":\"sQuKCKOF7DcNewSuje9qPEWQtMwHFluB4XJJ\",\"certificate\":\"-----BEGIN ENCRYPTED PRIVATE KEY-----\\nMIIE6zAdBgoqhkiG9w0BDAEDMA8ECKBjBeczxtkRAgMCAAAEggTIGZk0IpBi6TIe\\nZvSXEgWeCw/xzwkMMEjQCSysPFDRPMdR7VVhCODmnI0fg8X90KxHsIw93+3YBB3U\\nsznuiApp5d7KYeMpR1drsRQ2WOyqZEynaX8u5WfzbUYH1FgxiosJcUCN0J/b9JmX\\nNcrfQGqmuEDYEvaygskUMxIs42FweCkswqcvDVVZkql72dKOfn6AJv5AB7teKzDO\\nhZFOq4FT/1mJHnhKKZxKpn+HdPg1ZUMprIKuprtIC03W12Q5pI6kdNuy0XBUnFfx\\nhMscDgYHabOATezCyLhCiFXv1M99Z2bp0d/oNskJ+dSbAzfjkgYJ6SvP3BfBI36F\\nBFnkapspiV2GKFWY9uciQvEX9Rl5P7oUFrv/KCd5GBcddZCbNaBmuKInj+GOcvMp\\nfjmGXdX3J5QizUjZ4zJGdXrDNDgWyvuh/CigmB2jYNo6heBuIcX+Pya74kOoJpQd\\nwRLXhLbMDihUgNCs7KMhcZGMNN4qcGka6P4k4zsYzbbFpHroaCaSLTTDJmSSuGCx\\nLh94LZvbjv3Gy+OIJ5HMZmXlOHKBS2WQxOC4YUif7bkLaZYa13exPEh6TiXZ5K6i\\nhgR+te2WzT72ugETBKS7iVl0UQzwzTcPQGCgVGr0oyI4XmYmEqIM1iBJ6/AcR7tI\\n6BAGo2PsuRsHv1m5ZOzstIAF1E565z2tADS4fGdx/GjmZkktujbCtjQBGPILkLgU\\n/6QXXNllz99T+0pLskPAxUdJwUdNIOvHU7dE+KGkVE9TI0Xdf7m2OZk3MoYQZO8j\\nG/SS7dyNN3NW0mYks3Z8HskQTCco/XrRInSer9K8aeMN2DYIQ/k+SB18/1IrywHO\\nx7TvUw3Xo8KtHgXxuMD0upU5p6LBGRdlAFz1/wVUDTOERHV/A6I0sdAa917OEAQv\\n8ayq/Eh6ThzsTRVnqJjSnLUjvncDKc5AGodKwG0IIGkcrU/Cm2Pzy8zo2vR28piV\\nLN7trImzcRU5e90VUqhV9aDBML2ROH4UGG7Ofam+EQL2Hz++7S6cMgllATqKKdfT\\nWO+FuYIAWsliu1CKUC0RW57wr2W7X4r+opTKhzNCNZGz7OqHNkM3GwePAFdouaTl\\nfk7Ex2+ZEgAuvBUyfeGG+L3iNTjoMOxzeYVq1czRnsNpFrMfQbxghKa8G+lvqgKe\\ns9WPByIA32CC8mJsl8qINDmVI3dnvnjWqI7MVz65m2MXKfeoli5ahSGltx8sfRAe\\nzQTez0TkN5V/3MB3qLpwdZmJbv+R9dkVdIJecAmtCU7o08TjJICiJPDPchNBEzOm\\nwHMAdSpDrAcHv9cnyptZrEO7LJSn3078uqToW2Qh2w2ClViPHiGnwOSkETR51Xdd\\nM2p/HONKz58q/p1QUHo9XPfaTCDtR7TEdyltYG/n3FcWEtH80Wm1+D/LjAGyxWqV\\nc3FFEFAakRb4xS9mk8r9bbopvAvo80P45QAW1JNJ1l0bUjYsEFdMNsO+wNlbHea0\\nVBAu77/qZON2EhPIsrPBiox5MnKQyWGSxLzkorJb4VjTJSCgDeru/CQ+yFMyaxH0\\n452783yrwne2sdU4FrFdGCDMAjMfcRD9MpYS/xSskBSV3SUweLOgBvLAO8iiU6mK\\nmzyjSh9c6XA2SqEgMd3V\\n-----END ENCRYPTED PRIVATE KEY-----\\n-----BEGIN CERTIFICATE-----\\nMIIEaDCCA1CgAwIBAgIPAJ7QHiN6gBegEAEC9RSyMA0GCSqGSIb3DQEBCwUAMFYx\\nCzAJBgNVBAYTAkRFMSMwIQYDVQQKExpTQVAgSW9UIFRydXN0IENvbW11bml0eSBJ\\nSTEiMCAGA1UEAxMZU0FQIEludGVybmV0IG9mIFRoaW5ncyBDQTAeFw0yMTAxMTQw\\nNTUxMzJaFw0yMjAxMTQwNTUxMzJaMIG1MQswCQYDVQQGEwJERTEcMBoGA1UEChMT\\nU0FQIFRydXN0IENvbW11bml0eTEVMBMGA1UECxMMSW9UIFNlcnZpY2VzMXEwbwYD\\nVQQDFGhkZXZpY2VBbHRlcm5hdGVJZDpkOWY3ZTg5NS1lMWRjLTRlMGYtODBjOC1l\\nNWRmMWRhZWEzMTh8Z2F0ZXdheUlkOjN8dGVuYW50SWQ6MTkwMjAwMTc4NXxpbnN0\\nYW5jZUlkOmRrZS1xYTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAIBN\\n+Sc6GqoDFGNbujnxTu68SYE9xKLo1DnwC2OKCIOb7y6BkUlBL7KyJqFsprcNQ8jU\\n/sSF+YQPiDvtEQNrctzgv+3DFXiQTA8kJ1rOpdMZKPEeUK4IbI1UcdLbala5T/+T\\nsIg3SyvWsWWrvItpAzT4CyiRbchFdNTjhkMAyAWt4gGXFBVt+9xXRUOldjFQNJ6a\\nBOfngwncbQ8O99Hhs0jCkZGe2antLUcZWwlHEEeXqvJmOPTwiwfk2Vhl20mpK2g/\\ndi/m5dMDdLx8b2DVa1bnFxOHf3D3P1AlYeLz91CZ6LhsX5xk3BNpIhi9ubozfHxJ\\nM5k8h9K7KUb0BNnPZqcCAwEAAaOB0jCBzzBIBgNVHR8EQTA/MD2gO6A5hjdodHRw\\nczovL3Rjcy5teXNhcC5jb20vY3JsL1RydXN0Q29tbXVuaXR5SUkvU0FQSW9UQ0Eu\\nY3JsMAwGA1UdEwEB/wQCMAAwJQYDVR0SBB4wHIYaaHR0cDovL3NlcnZpY2Uuc2Fw\\nLmNvbS9UQ1MwDgYDVR0PAQH/BAQDAgbAMB0GA1UdDgQWBBTiL0tm/zHbX8HwLgA/\\nfK13HBPQvTAfBgNVHSMEGDAWgBSVt7P1WN7VtLNYRuDypsl4Tr0tdTANBgkqhkiG\\n9w0BAQsFAAOCAQEAKv2ME27NCvM7VJyKisY3fP+rHF+52TN6QxenBXGmuH/0n3Tq\\nDqgZfD/4H3PzJiQQOAKTagj1IROfJ8oRRxJWK+N7VyEWoMw67V53f7R0gzOf/8rb\\nGrP9ZsgK5R+WzBsYZH1dNHvztKhfoufxUfJxiaxXxbdLykKow/nXwdOHSKKus6/t\\nWUIMRhxkFm8fw4BaPeqqfllh+s3Gm25Z2SxhfBfvrhO8YWlNUxK96OQOgNg+XjgB\\nllR0lqekWh6w6yESnLTEFxroFyN2xIsmLBWwDiVdQfQVxU6jJ88dHFWcTnzu5Ht3\\nIqRfTIH6MaYOdvkCBAjh1wnxS9xRrOXmFncugA==\\n-----END CERTIFICATE-----\\n\"}}";
    }
}