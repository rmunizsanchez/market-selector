<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/markets', \Nitsnets\MarketSelector\Http\Controllers\getMarketsController::class)
->name('switch_markets.getMarkets');

Route::post('/switch', \Nitsnets\MarketSelector\Http\Controllers\MarketSelector::class)
->name('switch_market.switchMarkets');
