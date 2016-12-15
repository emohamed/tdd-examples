<?php
use PHPUnit\Framework\TestCase;
use Mockery as m;

require_once __DIR__ . '/../src/DeliveryDateCalculator.php';
require_once __DIR__ . '/../src/HolidaySchedule.php';

class DeliveryDateCalculatorTest extends TestCase
{
    public $calc;

    public function setup() {
        $this->calc = new DeliveryDateCalculator();
        
        $holiday_schedule_mock = m::mock('HolidaySchedule');
        $holiday_schedule_mock
            ->shouldReceive('is_holiday')
            ->andReturnUsing(function ($date) {
               return in_array($date->format('m-d'), ['01-01', '03-03', '07-04']);
            });

        $this->calc->holiday_schedule = $holiday_schedule_mock;
    }

    public function tearDown() {
        m::close();
    }

    public function provideTestData() {
        return [
            [
                'base_date'     => '2016-11-21 12:24:15 EEST',
                'shift'         => '1',
                'expected_date' => '2016-11-22',
                'message'       => 'it calculates with one day',
            ],
            [
                'base_date'     => '2016-11-21 12:24:15 EEST',
                'shift'         => '5',
                'expected_date' => '2016-11-28',
                'message'       => 'calculates with five days',
            ],
            [
                'base_date'     => '2016-11-19 12:24:15 EEST',
                'shift'         => '1',
                'expected_date' => '2016-11-21',
                'message'       => 'calculates correct date when starting on holiday',
            ],
            [
                'base_date'     => '2017-07-03 12:24:15 EEST',
                'shift'         => '1',
                'expected_date' => '2017-07-05',
                'message'       => 'skips bank holidays',
            ],

            [
                'base_date'     => '2017-07-04 12:24:15 EEST',
                'shift'         => '1',
                'expected_date' => '2017-07-05',
                'message'       => 'counts correctly when starting on bank holiday',
            ],

            [
                'base_date'     => '2016-11-21 07:24:15 EEST',
                'shift'         => '1',
                'expected_date' => '2016-11-21',
                'message'       => 'includes today in delivery time before 9AM sofia time',
            ],

            [
                'base_date'     => '2016-11-19 07:24:15 EEST',
                'shift'         => '1',
                'expected_date' => '2016-11-21',
                'message'       => 'doesnt care at what time it starts when starting on holiday',
            ],
        ];
    }

    /**
     * @dataProvider provideTestData
     */
    public function testShifts($base_date, $shift, $expected_date, $message) {
        $this->calc->setTime(new DateTimeImmutable($base_date));
        $delivery_date = $this->calc->calculate($shift);
        $this->assertEquals($expected_date, $delivery_date->format('Y-m-d'), $message);
    }

    public function test_double_calculation() {
        $this->calc->setTime(new DateTimeImmutable('2016-11-21 12:24:15 EEST'));

        $delivery_date = $this->calc->calculate(1);
        $this->assertEquals('2016-11-22', $delivery_date->format('Y-m-d'));

        $delivery_date = $this->calc->calculate(1);
        $this->assertEquals('2016-11-22', $delivery_date->format('Y-m-d'));
    }

}
