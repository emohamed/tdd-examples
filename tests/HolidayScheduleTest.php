<?php
use PHPUnit\Framework\TestCase;
use Mockery as m;

require_once __DIR__ . '/../src/HolidaySchedule.php';

class HolidayScheduleTest extends TestCase
{
    public function setup() {
        $this->holidaySchedule = new HolidaySchedule();
        $this->holidaySchedule->cache = new DummyCache();
    }

    public function tearDown() {
        m::close();
    }

    /** @test */
    public function basic_case() {
        $mock = m::mock('\GuzzleHttp\Client');
        $mock->shouldReceive([
            'request->getBody' => file_get_contents(__DIR__ . '/mock-data/schedule-2017.html'),
        ]);
        $this->holidaySchedule->http_client = $mock;

        $july4th = new DateTime("2017-07-04");
        $this->assertTrue($this->holidaySchedule->is_holiday($july4th));
        $july4th = new DateTime("2017-07-03");
        $this->assertFalse($this->holidaySchedule->is_holiday($july4th));
    }

    /**
     * @test
     * @expectedException HolidayScheduleException
     * @expectedExceptionMessage Could not fetch holidays for
     */
    public function not_found_errors_throw_exceptions() {
        $mock = m::mock('\GuzzleHttp\Client');
        $mock->shouldReceive('request')
            ->andThrow('GuzzleHttp\Exception\TransferException');

        $this->holidaySchedule->http_client = $mock;
        $this->holidaySchedule->is_holiday(new DateTime());
    }
}
