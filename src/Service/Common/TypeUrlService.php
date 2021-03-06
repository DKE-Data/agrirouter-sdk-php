<?php declare(strict_types=1);

namespace App\Service\Common {

    use Agrirouter\Commons\Messages;
    use Agrirouter\Feed\Response\HeaderQueryResponse;
    use Agrirouter\Feed\Response\MessageQueryResponse;
    use Agrirouter\Response\Payload\Account\ListEndpointsResponse;
    use Google\Protobuf\Internal\DescriptorPool;

    /**
     * Manage type URLs from Protobuf files.
     * @package App\Service\Common
     */
    class TypeUrlService
    {
        private const PREFIX = "types.agrirouter.com/";

        private static bool $metadataHasBeenInitialized = false;

        /**
         * Get the type URL for the given class.
         * @param mixed $clazz The class to return the type URL for.
         * @return string The type URL for the given type.
         * @noinspection PhpMissingParamTypeInspection
         */
        public static function getTypeUrl($clazz): string
        {
            self::registerMetadataObjectsForResponseTypes();
            $pool = DescriptorPool::getGeneratedPool();
            $descriptorByClassName = $pool->getDescriptorByClassName($clazz);
            $fullName = $descriptorByClassName->getFullName();
            return self::PREFIX . $fullName;
        }

        private static function registerMetadataObjectsForResponseTypes()
        {
            self::$metadataHasBeenInitialized = true;
            new Messages();
            new ListEndpointsResponse();
            new HeaderQueryResponse();
            new MessageQueryResponse();
        }

    }
}