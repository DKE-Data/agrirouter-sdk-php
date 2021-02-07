<?php declare(strict_types=1);


namespace App\Service\Common {

    use Agrirouter\Commons\Messages;
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
        }


    }
}