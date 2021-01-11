<?php namespace Service\Common;

use PHPUnit\Framework\TestCase;

class UtcDataServiceTest extends TestCase
{
    /**
     * @covers \Service\Common\UtcDataService::now
     */
    function testGivenAnyPointInTimeThenAskingTheUtcDataServiceForTheCurrentTimeThenTheResultShouldHaveFixedLength()
    {
        $utcDataService = new UtcDataService();
        $timestamp = $utcDataService->now();
        self::assertEquals(24, strlen($timestamp));
    }

}