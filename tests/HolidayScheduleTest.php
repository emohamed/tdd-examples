<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../src/HolidaySchedule.php';

class HolidayScheduleTest extends TestCase
{
    public function setup() {
        $this->holidaySchedule = new HolidaySchedule();
    }

    public function tearDown() {

    }

    /** @test */
    public function basic_case() {
        $july4th = new DateTime("2017-07-04");
        $this->assertTrue($this->holidaySchedule->is_holiday($july4th));
        $july4th = new DateTime("2017-07-03");
        $this->assertFalse($this->holidaySchedule->is_holiday($july4th));
    }

    /** @test */
    public function not_found_errors_throw_exceptions() {
        $mocked_http_client;
        $this->holidaySchedule->http_client = $mocked_http_client;
    }
}
