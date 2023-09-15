<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
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
        $baseCurrency = config('app.currency', 'USD');

        $locale = config('app.locale', 'en');
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        if ($currency == null) {
            // if (isNull($currency)) {
            $currency = Session::get('currency_code', $baseCurrency);
        }


        if ($currency != $baseCurrency) {
            $rate = Cache::get('currency_rate_' . $currency, 1);
            $amount = $amount * $rate;
        }

        return $formatter->formatCurrency($amount, $currency);
    }
}
