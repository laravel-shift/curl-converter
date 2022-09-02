<?php

namespace Shift\CurlConverter\Console\Commands;

use Illuminate\Console\Command;
use Shift\CurlConverter\Support\HttpCall;

class CurlCommand extends Command
{
    protected $signature = 'shift:curl {--X|request=GET} {--H|header=*} {--d|data=*} {--F|form=*} {--digest} {--basic} {--connect-timeout=} {--retry=} {--s|silent} {--u|user=} {url}';

    protected $description = 'Convert a UNIX curl request to an HTTP Client request';

    public function handle()
    {
        $request = \Shift\CurlConverter\Models\Request::create($this->gatherOptions());
        $code = HttpCall::format($request);

        $this->line($code);

        return 0;
    }

    private function gatherOptions()
    {
        return [
            'method' => $this->option('request'),
            'url' => $this->argument('url'),
            'headers' => $this->option('header'),
            'data' => $this->option('data'),
            'fields' => $this->option('form'),
            'digest' => $this->option('digest'),
            'basic' => $this->option('basic'),
            'timeout' => $this->option('connect-timeout'),
            'retry' => $this->option('retry'),
            'silent' => $this->option('silent'),
            'user' => $this->option('user'),
            // TODO: map more options...
        ];
    }
}
