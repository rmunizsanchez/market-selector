<?php

namespace Nitsnets\MarketSelector\Http\Controllers;

use Illuminate\Http\Request;
use \Nova;

final class getMarketsController
{
    public function __invoke()
    {
        $type = \Nitsnets\MarketSelector\MarketSelector::class;
        $tool = collect(Nova::registeredTools())
            ->first(function ($tool) use ($type) {
                return $tool instanceof $type;
            });
        $result = optional($tool)->getMarkets() ?? [];
        $market = data_get($result, 'markets', []);
        $current = data_get($result, 'current', '');
        return response()->json(['markets' => $market, 'current' => $current]);
    }
}
