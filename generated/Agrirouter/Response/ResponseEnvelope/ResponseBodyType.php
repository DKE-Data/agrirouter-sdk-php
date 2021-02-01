<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: messaging/response/response.proto

namespace Agrirouter\Response\ResponseEnvelope;

use Agrirouter\Response\ResponseEnvelope_ResponseBodyType;
use UnexpectedValueException;

/**
 * Protobuf type <code>agrirouter.response.ResponseEnvelope.ResponseBodyType</code>
 */
class ResponseBodyType
{
    /**
     * Generated from protobuf enum <code>MESSAGES = 0;</code>
     */
    const MESSAGES = 0;
    /**
     * Represents a successful acknowledgement of a message sent to the agrirouter
     *
     * Generated from protobuf enum <code>ACK = 1;</code>
     */
    const ACK = 1;
    /**
     * Represents a successful acknowledgement of a message sent to the agrirouter but contains some info or warn messages back
     *
     * Generated from protobuf enum <code>ACK_WITH_MESSAGES = 2;</code>
     */
    const ACK_WITH_MESSAGES = 2;
    /**
     * Represents a failure response in correlation to a message sent to the agrirouter
     *
     * Generated from protobuf enum <code>ACK_WITH_FAILURE = 3;</code>
     */
    const ACK_WITH_FAILURE = 3;
    /**
     * Used for acknowledgements for feed envelope requests and cantain the agrirouter.feed.response.EnvelopeResponse details
     *
     * Generated from protobuf enum <code>ACK_FOR_FEED_HEADER_LIST = 6;</code>
     */
    const ACK_FOR_FEED_HEADER_LIST = 6;
    /**
     * Used for acknowledgements for feed payload requests and cantain the agrirouter.feed.response.PayloadResponse details
     *
     * Generated from protobuf enum <code>ACK_FOR_FEED_MESSAGE = 7;</code>
     */
    const ACK_FOR_FEED_MESSAGE = 7;
    /**
     * Response for failed requests to feed queries
     *
     * Generated from protobuf enum <code>ACK_FOR_FEED_FAILED_MESSAGE = 8;</code>
     */
    const ACK_FOR_FEED_FAILED_MESSAGE = 8;
    /**
     * Used for a response which contains the endpoint listing query results
     *
     * Generated from protobuf enum <code>ENDPOINTS_LISTING = 10;</code>
     */
    const ENDPOINTS_LISTING = 10;
    /**
     * Used for the response body type for agrirouter.cloud.registration.OnboardingResponse
     *
     * Generated from protobuf enum <code>CLOUD_REGISTRATIONS = 11;</code>
     */
    const CLOUD_REGISTRATIONS = 11;
    /**
     * Used for push notification messages
     *
     * Generated from protobuf enum <code>PUSH_NOTIFICATION = 12;</code>
     */
    const PUSH_NOTIFICATION = 12;

    private static $valueToName = [
        self::MESSAGES => 'MESSAGES',
        self::ACK => 'ACK',
        self::ACK_WITH_MESSAGES => 'ACK_WITH_MESSAGES',
        self::ACK_WITH_FAILURE => 'ACK_WITH_FAILURE',
        self::ACK_FOR_FEED_HEADER_LIST => 'ACK_FOR_FEED_HEADER_LIST',
        self::ACK_FOR_FEED_MESSAGE => 'ACK_FOR_FEED_MESSAGE',
        self::ACK_FOR_FEED_FAILED_MESSAGE => 'ACK_FOR_FEED_FAILED_MESSAGE',
        self::ENDPOINTS_LISTING => 'ENDPOINTS_LISTING',
        self::CLOUD_REGISTRATIONS => 'CLOUD_REGISTRATIONS',
        self::PUSH_NOTIFICATION => 'PUSH_NOTIFICATION',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResponseBodyType::class, ResponseEnvelope_ResponseBodyType::class);
