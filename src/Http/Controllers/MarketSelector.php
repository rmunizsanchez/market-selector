<?php

namespace Nitsnets\MarketSelector\Http\Controllers;

use Illuminate\Http\Request;
use \Nova;

final class MarketSelector
{
    public function __invoke(Request $request)
    {
        $current = $request->post('value');
    
        $market = collect(config('nova-permissions.permissions'))
            ->where('group', 'Market')
            ->where('value', $current)
            ->first();
        $prefix = optional(auth()->user())->id;
        if ($market) {
            cache()->set($prefix . '.locale', data_get($market, 'locale'));
            cache()->set($prefix . '.market', data_get($market, 'value'));
            cache()->set($prefix . '.timezone', data_get($market, 'timezone'));
            app()->setLocale(data_get($market, 'locale'));
        }
        \Session::put('current_market', $request->post('value'));
        $alternative = 2;
        switch ($current) {
            case 1:
                $alternative =2;
                break;
            case 2:
            case 6:
            case 7:
                $alternative = 1;
                break;
        }
        \Session::put('current_market_alternative', $alternative);
        config('nova.name', 'Sportzone');
    }
}
