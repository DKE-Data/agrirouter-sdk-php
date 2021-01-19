<?php namespace Lib\Tests\Service\Common {

    use App\Service\Common\UtcDataService;
    use DateTime;
    use DateTimeZone;
    use PHPUnit\Framework\TestCase;

    class UtcDataServiceTest extends TestCase
    {
        function testGivenAnyPointInTimeThenAskingTheUtcDataServiceForTheCurrentTimeThenTheResultShouldHaveFixedLength()
        {
            $timestamp = UtcDataService::now();
            self::assertEquals(24, strlen($timestamp));
        }

        function testGivenValidOffsetWhenAskingTheUtcDataServiceForTheTimeZOneThenTheResultShouldBeAvailable()
        {
            $offset = timezone_offset_get(new DateTimeZone('Pacific/Kiritimati'), new DateTime());
            $timeZone = UtcDataService::timeZone($offset);
            self::assertEquals(6, strlen($timeZone));
            self::assertEquals("+14:00", $timeZone);

            $offset = timezone_offset_get(new DateTimeZone('Europe/Berlin'), new DateTime());
            $timeZone = UtcDataService::timeZone($offset);
            self::assertEquals(6, strlen($timeZone));
            self::assertEquals("+01:00", $timeZone);

            $offset = timezone_offset_get(new DateTimeZone('America/Los_Angeles'), new DateTime());
            $timeZone = UtcDataService::timeZone($offset);
            self::assertEquals(6, strlen($timeZone));
            self::assertEquals("-08:00", $timeZone);
        }

    }
}