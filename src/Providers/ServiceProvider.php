<?php

namespace Shift\CurlConverter\Providers;

use Shift\CurlConverter\Console\Commands\CurlCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->commands([
            CurlCommand::class,
        ]);
    }
}
