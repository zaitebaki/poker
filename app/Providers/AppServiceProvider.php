<?php

namespace Poker\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Функция set осуществляет присваивание в шаблоне blade
         * Возвращает php-строку
         * @param string $exp
         * @return string
         */
        Blade::directive('set', function ($exp) {

            list($name, $val) = explode(',', $exp);
            return "<?php $name = $val ?>";

        });
    }
}
