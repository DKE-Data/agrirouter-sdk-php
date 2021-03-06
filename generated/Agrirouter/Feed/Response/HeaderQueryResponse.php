<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: messaging/response/payload/feed/feed-response.proto

namespace Agrirouter\Feed\Response;

use Agrirouter\Commons\ChunkComponent;
use Agrirouter\Feed\Response\HeaderQueryResponse\Feed;
use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\Message;
use Google\Protobuf\Internal\RepeatedField;
use GPBMetadata\Messaging\Response\Payload\Feed\FeedResponse;

/**
 * Generated from protobuf message <code>agrirouter.feed.response.HeaderQueryResponse</code>
 */
class HeaderQueryResponse extends Message
{
    /**
     * Refer to Statistics
     *
     * Generated from protobuf field <code>.agrirouter.feed.response.QueryMetrics query_metrics = 1;</code>
     */
    protected $query_metrics = null;
    /**
     * Refer to Paging
     *
     * Generated from protobuf field <code>.agrirouter.feed.response.Page page = 2;</code>
     */
    protected $page = null;
    /**
     * Refer to agrirouter.commons.ChunkComponent
     *
     * Generated from protobuf field <code>repeated .agrirouter.commons.ChunkComponent chunk_contexts = 3;</code>
     */
    private $chunk_contexts;
    /**
     * Refer to Feed
     *
     * Generated from protobuf field <code>repeated .agrirouter.feed.response.HeaderQueryResponse.Feed feed = 4;</code>
     */
    private $feed;
    /**
     * List of message ids in pending confirmation status
     *
     * Generated from protobuf field <code>repeated string pending_message_ids = 5 [deprecated = true];</code>
     */
    private $pending_message_ids;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type QueryMetrics $query_metrics
     *           Refer to Statistics
     *     @type Page $page
     *           Refer to Paging
     *     @type ChunkComponent[]|RepeatedField $chunk_contexts
     *           Refer to agrirouter.commons.ChunkComponent
     *     @type Feed[]|RepeatedField $feed
     *           Refer to Feed
     *     @type string[]|RepeatedField $pending_message_ids
     *           List of message ids in pending confirmation status
     * }
     */
    public function __construct($data = NULL) {
        FeedResponse::initOnce();
        parent::__construct($data);
    }

    /**
     * Refer to Statistics
     *
     * Generated from protobuf field <code>.agrirouter.feed.response.QueryMetrics query_metrics = 1;</code>
     * @return QueryMetrics
     */
    public function getQueryMetrics()
    {
        return $this->query_metrics;
    }

    /**
     * Refer to Statistics
     *
     * Generated from protobuf field <code>.agrirouter.feed.response.QueryMetrics query_metrics = 1;</code>
     * @param QueryMetrics $var
     * @return $this
     */
    public function setQueryMetrics($var)
    {
        GPBUtil::checkMessage($var, QueryMetrics::class);
        $this->query_metrics = $var;

        return $this;
    }

    /**
     * Refer to Paging
     *
     * Generated from protobuf field <code>.agrirouter.feed.response.Page page = 2;</code>
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Refer to Paging
     *
     * Generated from protobuf field <code>.agrirouter.feed.response.Page page = 2;</code>
     * @param Page $var
     * @return $this
     */
    public function setPage($var)
    {
        GPBUtil::checkMessage($var, Page::class);
        $this->page = $var;

        return $this;
    }

    /**
     * Refer to agrirouter.commons.ChunkComponent
     *
     * Generated from protobuf field <code>repeated .agrirouter.commons.ChunkComponent chunk_contexts = 3;</code>
     * @return RepeatedField
     */
    public function getChunkContexts()
    {
        return $this->chunk_contexts;
    }

    /**
     * Refer to agrirouter.commons.ChunkComponent
     *
     * Generated from protobuf field <code>repeated .agrirouter.commons.ChunkComponent chunk_contexts = 3;</code>
     * @param ChunkComponent[]|RepeatedField $var
     * @return $this
     */
    public function setChunkContexts($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, GPBType::MESSAGE, ChunkComponent::class);
        $this->chunk_contexts = $arr;

        return $this;
    }

    /**
     * Refer to Feed
     *
     * Generated from protobuf field <code>repeated .agrirouter.feed.response.HeaderQueryResponse.Feed feed = 4;</code>
     * @return RepeatedField
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Refer to Feed
     *
     * Generated from protobuf field <code>repeated .agrirouter.feed.response.HeaderQueryResponse.Feed feed = 4;</code>
     * @param Feed[]|RepeatedField $var
     * @return $this
     */
    public function setFeed($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, GPBType::MESSAGE, Feed::class);
        $this->feed = $arr;

        return $this;
    }

    /**
     * List of message ids in pending confirmation status
     *
     * Generated from protobuf field <code>repeated string pending_message_ids = 5 [deprecated = true];</code>
     * @return RepeatedField
     */
    public function getPendingMessageIds()
    {
        return $this->pending_message_ids;
    }

    /**
     * List of message ids in pending confirmation status
     *
     * Generated from protobuf field <code>repeated string pending_message_ids = 5 [deprecated = true];</code>
     * @param string[]|RepeatedField $var
     * @return $this
     */
    public function setPendingMessageIds($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, GPBType::STRING);
        $this->pending_message_ids = $arr;

        return $this;
    }

}

