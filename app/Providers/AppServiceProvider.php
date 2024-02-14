<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

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
        // DB::listen(function($query) {
        //     Log::info(
        //         $query->sql,
        //         $query->bindings,
        //         $query->time
        //     );
        // });

        // DB::listen(function($query) {
        //     $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL;

        //     File::append(
        //         storage_path('/logs/query.log'),
        //         $logMessage
        //     );
        // });

    }
}
