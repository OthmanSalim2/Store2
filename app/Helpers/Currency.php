<?php

namespace App\Helpers;

use NumberFormatter;

// use function PHPUnit\Framework\isNull;


class Currency
{

    // this method use to if called the class as function.
    // this magic method
    public function __invoke(...$params)
    {
        // here too I convert array to arguments.
        return static::format(...$params);
    }

    public function format($amount, $currency = null)
    {
        $locale = config('app.locale', 'en');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        if ($currency == null) {
            // if (isNull($currency)) {
            $currency = config('app.currency', 'USD');
        }
        return $formatter->formatCurrency($amount, $currency);
    }
}
