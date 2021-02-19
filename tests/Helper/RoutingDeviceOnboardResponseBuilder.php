<?php declare(strict_types=1);

namespace Lib\Tests\Helper {

    use App\Dto\Onboard\Authentication;
    use App\Dto\Onboard\ConnectionCriteria;
    use App\Dto\Onboard\OnboardResponse;
    use Lib\Tests\Helper\Exception\RoutingDeviceOnboardResponseBuilderException;

    /**
     * Builds a router device onboard reponse for a specific endpoint
     *(@see https://github.com/DKE-Data/agrirouter-interface-documentation/blob/develop/docs/router-devices.adoc)
     * @package Lib\Tests\Helper
     */
    class RoutingDeviceOnboardResponseBuilder
    {
        private OnboardResponse $routingDeviceOnboardResponse;
        private OnboardResponse $clientDeviceOnboardResponse;

        public function withRouterDeviceOnboardResponse(OnboardResponse $onboardResponse): self
        {
            $this->routingDeviceOnboardResponse = $onboardResponse;
            $this->routingDeviceOnboardResponse->getConnectionCriteria()->setClientId($this->routingDeviceOnboardResponse->getDeviceAlternateId());
            return $this;
        }

        public function withClientOnboardResponse(OnboardResponse $onboardResponse): self
        {
            $this->clientDeviceOnboardResponse = $onboardResponse;
            return $this;
        }

        /**
         * @return OnboardResponse
         * @throws RoutingDeviceOnboardResponseBuilderException
         */
        public function build(): OnboardResponse
        {
            if (empty($this->routingDeviceOnboardResponse)) {
                throw (new RoutingDeviceOnboardResponseBuilderException("Assembling of OnboardResponse failed. Data for routing device is missing.", 666));
            } elseif (empty($this->clientDeviceOnboardResponse)) {
                return $this->routingDeviceOnboardResponse;
            }

            return $this->mergeOnboardResponses();
        }

        /**
         * @return OnboardResponse
         */
        private function mergeOnboardResponses(): OnboardResponse
        {
            $mergedOnboardResponse = new OnboardResponse();
            $mergedOnboardResponse->setAuthentication($this->cloneAuthentification($this->routingDeviceOnboardResponse->getAuthentication()));
            $mergedOnboardResponse->setConnectionCriteria($this->mergeConnectionCriteria(
                $this->routingDeviceOnboardResponse->getConnectionCriteria(),
                $this->clientDeviceOnboardResponse->getConnectionCriteria(),
                $this->routingDeviceOnboardResponse->getDeviceAlternateId()));
            $mergedOnboardResponse->setDeviceAlternateId($this->routingDeviceOnboardResponse->getDeviceAlternateId());
            $mergedOnboardResponse->setCapabilityAlternateId($this->clientDeviceOnboardResponse->getCapabilityAlternateId());
            $mergedOnboardResponse->setSensorAlternateId($this->clientDeviceOnboardResponse->getSensorAlternateId());
            return $mergedOnboardResponse;

        }

        /**
         * @param Authentication $authentication
         * @return Authentication
         */
        private function cloneAuthentification(Authentication $authentication): Authentication
        {
            $clonedAuthentification = new Authentication();
            $clonedAuthentification->setCertificate($authentication->getCertificate());
            $clonedAuthentification->setSecret($authentication->getSecret());
            $clonedAuthentification->setType($authentication->getType());
            return $clonedAuthentification;
        }

        /**
         * @param ConnectionCriteria $routingDeviceConnectionCriteria
         * @param ConnectionCriteria $clientDeviceConnectionCriteria
         * @param string $routingDeviceAlternateId
         * @return ConnectionCriteria
         */
        private function mergeConnectionCriteria(ConnectionCriteria $routingDeviceConnectionCriteria,
                                                 ConnectionCriteria $clientDeviceConnectionCriteria,
                                                 string $routingDeviceAlternateId): ConnectionCriteria
        {
            $mergedConnectionCriteria = new ConnectionCriteria();
            $mergedConnectionCriteria->setClientId($routingDeviceAlternateId);
            $mergedConnectionCriteria->setHost($routingDeviceConnectionCriteria->getHost());
            $mergedConnectionCriteria->setPort($routingDeviceConnectionCriteria->getPort());
            $mergedConnectionCriteria->setGatewayId($routingDeviceConnectionCriteria->getGatewayId());
            $mergedConnectionCriteria->setCommands($clientDeviceConnectionCriteria->getCommands());
            $mergedConnectionCriteria->setMeasures($clientDeviceConnectionCriteria->getMeasures());

            return $mergedConnectionCriteria;
        }

    }
}