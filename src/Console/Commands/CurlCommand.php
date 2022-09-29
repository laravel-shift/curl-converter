<?php

namespace Shift\CurlConverter\Console\Commands;

use Illuminate\Console\Command;
use Shift\CurlConverter\Support\HttpCall;

class CurlCommand extends Command
{
    protected $signature = 'shift:curl {--X|request=} {--G|get} {--H|header=*} {--d|data=*} {--data-urlencode=*} {--F|form=*} {--digest} {--basic} {--connect-timeout=} {--max-timeout=} {--retry=} {--s|silent} {--u|user=} {url}';

    protected $description = 'Convert a UNIX curl request to an HTTP Client request';

    public function handle()
    {
        $request = \Shift\CurlConverter\Models\Request::create($this->gatherOptions());
        $code = HttpCall::format($request);

        $this->newLine();
        $this->line($code);
        $this->newLine();

        return 0;
    }

    private function gatherOptions()
    {
        return [
            'method' => $this->option('get') ? 'GET' : $this->option('request'),
            'url' => $this->argument('url'),
            'headers' => $this->option('header'),
            'data' => $this->option('data'),
            'dataUrlEncode' => $this->option('data-urlencode'),
            'fields' => $this->option('form'),
            'digest' => $this->option('digest'),
            'basic' => $this->option('basic'),
            'connectTimeout' => $this->option('connect-timeout'),
            'maxTimeout' => $this->option('max-timeout'),
            'retry' => $this->option('retry'),
            'silent' => $this->option('silent'),
            'user' => $this->option('user'),
        ];
    }
}
