<?php

namespace App\Tests\Helper;

use App\Helper\CommissionCalculator;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @dataProvider calculateCommissionProvider
     */
    public function testCalculateCommission($countryCode, $currency, $rate, $amount, $expected)
    {
        $calculator = new CommissionCalculator();

        if ($countryCode !== null) {
            $calculator->setCountryCode($countryCode);
        }

        if ($currency !== null) {
            $calculator->setCurrency($currency);
        }

        if ($rate !== null) {
            $calculator->setRate($rate);
        }

        if ($amount !== null) {
            $calculator->setAmount($amount);
        }

        if (is_subclass_of($expected, Exception::class) || is_a($expected, Exception::class, true)) {
            $this->expectException($expected);
        }

        $this->assertEquals($expected, $calculator->calculateCommission());
    }

    public static function calculateCommissionProvider()
    {
        return [
            // European country, EUR currency
            ['DE', 'EUR', 1, 100, 1.00],
            ['IT', 'EUR', 1, 100, 1.00],
            ['BG', 'EUR', 1, 100, 1.00],

            // Non-European country, EUR currency
            ['US', 'EUR', 1, 100, 2.00],
            ['JP', 'EUR', 1, 100, 2.00],

            // European country, non-EUR currency, rate > 0
            ['DE', 'USD', 1.5, 100, 0.67],
            ['IT', 'CAD', 2.5, 100, 0.40],

            // Non-European country, non-EUR currency, rate > 0
            ['US', 'GBP', 1.5, 100, 1.34],
            ['JP', 'AUD', 2.5, 100, 0.80],

            // Missing input parameters
            [null, 'EUR', 1, 100, Exception::class],
            ['DE', null, 1, 100, Exception::class],
            ['DE', 'EUR', null, 100, Exception::class],
            ['DE', 'EUR', 1, null, Exception::class],
        ];
    }

    public function testIsEuropeanCountry(): void
    {
        $method = new ReflectionMethod(CommissionCalculator::class, 'isEuropeanCountry');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs(null, ['AT']));
        $this->assertTrue($method->invokeArgs(null, ['FR']));
        $this->assertFalse($method->invokeArgs(null, ['US']));
    }
}
