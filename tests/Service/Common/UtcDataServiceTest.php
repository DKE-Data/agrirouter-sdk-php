<?php namespace Lib\Tests\Service\Common {

    use App\Service\Common\UtcDataService;
    use PHPUnit\Framework\TestCase;

    class UtcDataServiceTest extends TestCase
    {
        /**
         * @covers \App\Service\Common\UtcDataService::now
         */
        function testGivenAnyPointInTimeThenAskingTheUtcDataServiceForTheCurrentTimeThenTheResultShouldHaveFixedLength()
        {
            $timestamp = UtcDataService::now();
            self::assertEquals(24, strlen($timestamp));
        }

    }
}