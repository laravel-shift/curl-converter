<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CurlCommand extends Command
{
    protected $signature = 'curl {--X|request=GET} {--H|header=*} {--d|data=*} {--F|form=*} {url}';

    protected $description = 'Parse UNIX curl command';

    public function handle()
    {
        $json = [
            'method' => $this->option('request'),
            'url' => $this->argument('url'),
            'headers' => $this->option('header'),
            'data' => $this->option('data'),
            'fields' => $this->option('form'),
            // TODO: map more options...
        ];

        $this->line(json_encode($json));

        return 0;
    }
}
