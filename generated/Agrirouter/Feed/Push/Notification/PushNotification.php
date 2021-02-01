<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: messaging/response/payload/feed/push-notification.proto

namespace Agrirouter\Feed\Push\Notification;

use Agrirouter\Feed\Push\Notification\PushNotification\FeedMessage;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\Message;
use Google\Protobuf\Internal\RepeatedField;

/**
 * Generated from protobuf message <code>agrirouter.feed.push.notification.PushNotification</code>
 */
class PushNotification extends Message
{
    /**
     * Collection of messages allocated to this notification
     *
     * Generated from protobuf field <code>repeated .agrirouter.feed.push.notification.PushNotification.FeedMessage messages = 1;</code>
     */
    private $messages;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type FeedMessage[]|RepeatedField $messages
     *           Collection of messages allocated to this notification
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Messaging\Response\Payload\Feed\PushNotification::initOnce();
        parent::__construct($data);
    }

    /**
     * Collection of messages allocated to this notification
     *
     * Generated from protobuf field <code>repeated .agrirouter.feed.push.notification.PushNotification.FeedMessage messages = 1;</code>
     * @return RepeatedField
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Collection of messages allocated to this notification
     *
     * Generated from protobuf field <code>repeated .agrirouter.feed.push.notification.PushNotification.FeedMessage messages = 1;</code>
     * @param FeedMessage[]|RepeatedField $var
     * @return $this
     */
    public function setMessages($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, GPBType::MESSAGE, FeedMessage::class);
        $this->messages = $arr;

        return $this;
    }

}
