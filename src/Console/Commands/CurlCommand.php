<?php

namespace Shift\CurlConverter\Console\Commands;

use Illuminate\Console\Command;
use Shift\CurlConverter\Support\HttpCall;

class CurlCommand extends Command
{
    protected $signature = 'shift:curl {--X|request=} {--G|get} {--H|header=*} {--d|data=*} {--data-urlencode=*} {--data-raw=*} {--F|form=*} {--digest} {--basic} {--connect-timeout=} {--max-timeout=} {--retry=} {--s|curl-silent} {--u|user=} {--L|location} {--compressed} {--k|insecure} {--E|cert=} {--key=} {url}';

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
            'basic' => $this->option('basic'),
            'cert' => $this->option('cert'),
            'compressed' => $this->option('compressed'),
            'connectTimeout' => $this->option('connect-timeout'),
            'data' => $this->option('data'),
            'dataUrlEncode' => $this->option('data-urlencode'),
            'digest' => $this->option('digest'),
            'fields' => $this->option('form'),
            'headers' => $this->option('header'),
            'insecure' => $this->option('insecure'),
            'key' => $this->option('key'),
            'maxTimeout' => $this->option('max-timeout'),
            'method' => $this->option('get') ? 'GET' : $this->option('request'),
            'rawData' => $this->option('data-raw'),
            'retry' => $this->option('retry'),
            'silent' => $this->option('curl-silent'),
            'url' => $this->argument('url'),
            'user' => $this->option('user'),
        ];
    }
}
