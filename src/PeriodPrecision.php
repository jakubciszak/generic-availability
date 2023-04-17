<?php

namespace Jakubciszak\GenericAvailability;

enum PeriodPrecision: string
{
    case YEAR = 'Y';
    case MONTH = 'Ym';
    case DAY = 'Ymd';
    case HOUR = 'YmdH';
    case MINUTE = 'YmdHi';
    case SECOND = 'YmdHis';
}
