<?php

namespace Jakubciszak\GenericAvailability\Tests;


use DateTimeImmutable;
use Exception;
use Jakubciszak\GenericAvailability\Period;
use Jakubciszak\GenericAvailability\PeriodPrecision;
use PHPUnit\Framework\TestCase;

class PeriodTest extends TestCase
{
    public function testNoOverlapping(): void
    {
        $period = new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01'));
        $period2 = new Period(new DateTimeImmutable('2022-03-02'), new DateTimeImmutable('2022-04-01'));
        self::assertFalse($period->overlaps($period2));
    }

    public function testOverlapping(): void
    {
        $period = new Period(new DateTimeImmutable('2022-02-01'), new DateTimeImmutable('2022-03-01'));
        $period2 = new Period(new DateTimeImmutable('2022-02-02'), new DateTimeImmutable('2022-02-22'));
        self::assertFalse($period->overlaps($period2));
    }

    /**
     * @dataProvider equalsProvider
     * @throws Exception
     */
    public function testEquals(
        string $from, string $to, string $compareFrom, string $compareTo, PeriodPrecision $precision
    ): void {
        $period = new Period(new DateTimeImmutable($from), new DateTimeImmutable($to));
        $period2 = new Period(new DateTimeImmutable($compareFrom), new DateTimeImmutable($compareTo));
        self::assertTrue($period->equals($period2, $precision));
    }

    public static function equalsProvider(): array
    {
        return [
            [
                'from' => '2022-02-01 12:01:21', 'to' => '2022-02-03 18:01:21',
                'compareFrom' => '2022-02-01 11:01:21', 'compareTo' => '2022-02-03 12:01:21',
                PeriodPrecision::DAY
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-02-01 22:12:21', 'compareTo' => '2022-02-03 22:10:21',
                PeriodPrecision::HOUR
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-02-01 22:01:11', 'compareTo' => '2022-02-03 22:01:31',
                PeriodPrecision::MINUTE
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-02-01 22:01:21', 'compareTo' => '2022-02-03 22:01:21',
                PeriodPrecision::SECOND],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-02-02 22:01:11', 'compareTo' => '2022-02-05 22:01:31',
                PeriodPrecision::MONTH
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2023-02-03 22:01:21',
                'compareFrom' => '2022-01-02 22:01:11', 'compareTo' => '2023-03-05 22:01:31',
                PeriodPrecision::YEAR
            ],

        ];
    }

    /**
     * @dataProvider notEqualsProvider
     * @throws Exception
     */
    public function testNotEquals(
        string $from, string $to, string $compareFrom, string $compareTo, PeriodPrecision $precision
    ): void {
        $period = new Period(new DateTimeImmutable($from), new DateTimeImmutable($to));
        $period2 = new Period(new DateTimeImmutable($compareFrom), new DateTimeImmutable($compareTo));
        self::assertFalse($period->equals($period2, $precision));
    }

    public static function notEqualsProvider(): array
    {
        return [
            [
                'from' => '2022-02-01 12:01:21', 'to' => '2022-02-03 18:01:21',
                'compareFrom' => '2022-02-02 11:01:21', 'compareTo' => '2022-02-03 12:01:21',
                PeriodPrecision::DAY
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-02-01 23:12:21', 'compareTo' => '2022-02-03 22:10:21',
                PeriodPrecision::HOUR
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-02-01 22:02:11', 'compareTo' => '2022-02-03 22:03:31',
                PeriodPrecision::MINUTE
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-02-01 22:01:23', 'compareTo' => '2022-02-03 22:01:24',
                PeriodPrecision::SECOND],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2022-02-03 22:01:21',
                'compareFrom' => '2022-03-02 22:01:11', 'compareTo' => '2022-04-05 22:01:31',
                PeriodPrecision::MONTH
            ],
            [
                'from' => '2022-02-01 22:01:21', 'to' => '2023-02-03 22:01:21',
                'compareFrom' => '2023-01-02 22:01:11', 'compareTo' => '2024-03-05 22:01:31',
                PeriodPrecision::YEAR
            ],

        ];
    }
}
