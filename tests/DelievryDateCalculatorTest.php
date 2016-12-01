<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../src/DeliveryDateCalculator.php';

class DeliveryDateCalculatorTest extends TestCase
{
    public $calc;

    public function setup() {
        $this->calc = new DeliveryDateCalculator();
        // $this->calc->time = strtotime('');
    }

    public function tearDown() {

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
        $this->calc->setTime(new DateTime($base_date));
        $delivery_date = $this->calc->calculate($shift);
        $this->assertEquals($expected_date, $delivery_date->format('Y-m-d'), $message);
    }

    public function test_double_calculation() {
        $this->calc->setTime(new DateTimeImmutable('2016-11-21 12:24:15 EEST'));

        $delivery_date = $this->calc->calculate(1);
        $this->assertEquals('2016-11-22', $delivery_date->format('Y-m-d'));

        $delivery_date = $this->calc->calculate(1);
        $this->assertEquals('2016-11-23', $delivery_date->format('Y-m-d'));
    }

}
