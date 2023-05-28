<?php

namespace Nitsnets\MarketSelector;

use App\Services\Configuration;
use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class MarketSelector extends Tool
{
    private $markets;
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        $this->markets = collect(config('nova-permissions.permissions'))
            ->where('group', 'Market')
            ->mapWithKeys(function ($value, $key) {
                return [$value['value'] => [
                        'key' => $key,
                        'description' => $value['description'],
                        'value' => $value['value'],
                        'locale' => $value['locale']
                    ]
                ];
            })
            ->all();
        $all = true;
        $markets = [];
        foreach ($this->markets as $key => $market) {
            if (!Auth::user()->hasPermissionTo($market['key'])) {
                $all = false;
            } else {
                $markets[$key] = $market;
            }
        }
        $prefix = optional(auth()->user())->id;
        
        if (!cache()->has($prefix.'.locale')) {
            if (count($markets) == 1) {
                $market = head($markets);
                cache()->set($prefix . '.locale', data_get($market, 'locale'));
                cache()->set($prefix . '.market', data_get($market, 'value'));
                cache()->set($prefix . '.timezone', data_get($market, 'timezone'));
            } else {
                $default = collect($markets)->where('default', true)->first();
                if ($default) {
                    cache()->set($prefix . '.locale', data_get($default, 'locale'));
                    cache()->set($prefix . '.market', data_get($default, 'value'));
                    cache()->set($prefix . '.timezone', data_get($default, 'timezone'));
                } else {
                    $market = head($markets);
                    cache()->set($prefix . '.locale', data_get($market, 'locale'));
                    cache()->set($prefix . '.market', data_get($market, 'value'));
                    cache()->set($prefix . '.timezone', data_get($market, 'timezone'));
                }
            }
        }
        $this->markets = $markets;
        //app()->setLocale(cache()->has($prefix . ".locale") ? cache()->get($prefix . ".locale") : app()->getLocale());
        Nova::provideToScript([
            "market" => cache()->has($prefix . ".market") ? cache()->get($prefix . ".market") : 1,
            "locale" => cache()->has($prefix . ".locale") ? cache()->get($prefix . ".locale") : app()->getLocale(),
            'timezone' => cache()->has($prefix . ".timezone") ? cache()->get($prefix . ".timezone") : 'Europe/Madrid',
        ]);
        Nova::script('market-selector', __DIR__.'/../dist/js/MarketSelector.js');
        Nova::style('market-selector', __DIR__.'/../dist/css/MarketSelector.css');
    }
    
    
    /**
     * @return array
     */
    public function getMarkets(): array
    {
        $prefix = optional(auth()->user())->id;
        $current = cache()->get($prefix . ".market");
        if (!$current) {
            $current = head(
                array_keys($this->markets)
            )?? '1';
            \Session::put('current_market', $current);
            
            $alternative = 2;
            switch ($current) {
                case 1:
                    $locale ='es';
                    $alternative =2;
                    break;
                case 2:
                case 6:
                case 7:
                    $locale ='en';
                    $alternative = 1;
                    break;
            }
    
            \Cache::forever($prefix.".locale", $locale);
            //app()->setLocale($locale);
            \Session::put('current_market_alternative', $alternative);
        }

        setCurrentMarket($current);
        
        return [
            'markets' => $this->markets,
            'current' => $current
        ];
    }
}
