<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: cloud-provider-integration/cloud-virtualized-app-registration.proto

namespace Agrirouter\Cloud\Registration;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\Message;
use Google\Protobuf\Internal\RepeatedField;
use GPBMetadata\CloudProviderIntegration\CloudVirtualizedAppRegistration;

/**
 * Generated from protobuf message <code>agrirouter.cloud.registration.OffboardingRequest</code>
 */
class OffboardingRequest extends Message
{
    /**
     * The IDs of the endpoints within the agrirouter that should be offboarded at least one valid endpointId is required
     *
     * Generated from protobuf field <code>repeated string endpoints = 1;</code>
     */
    private $endpoints;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string[]|RepeatedField $endpoints
     *           The IDs of the endpoints within the agrirouter that should be offboarded at least one valid endpointId is required
     * }
     */
    public function __construct($data = NULL) {
        CloudVirtualizedAppRegistration::initOnce();
        parent::__construct($data);
    }

    /**
     * The IDs of the endpoints within the agrirouter that should be offboarded at least one valid endpointId is required
     *
     * Generated from protobuf field <code>repeated string endpoints = 1;</code>
     * @return RepeatedField
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * The IDs of the endpoints within the agrirouter that should be offboarded at least one valid endpointId is required
     *
     * Generated from protobuf field <code>repeated string endpoints = 1;</code>
     * @param string[]|RepeatedField $var
     * @return $this
     */
    public function setEndpoints($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, GPBType::STRING);
        $this->endpoints = $arr;

        return $this;
    }

}
