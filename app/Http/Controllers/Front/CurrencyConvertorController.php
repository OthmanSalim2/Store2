<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CurrencyConvertor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class CurrencyConvertorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => ['required', 'size:3', 'string'],
        ]);

        $baseCurrencyCode = config('app.currency');
        $currencyCode = $request->input('currency_code');

        $cacheKey = 'currency_rate_' . $currencyCode;
        $rate = Cache::get($cacheKey, 0);

        if (!$rate) {
            $convertor = app('currency.convertor');
            // $convertor = App::make('currency.convertor');
            $rate = $convertor->convert($baseCurrencyCode, $currencyCode);
            // this's way will be sharing for everybody.
            Cache::put($cacheKey, $rate, now()->addMinutes(60));
        }

        Session::put('currency_code', $currencyCode);

        return redirect()->back();
    }
}
