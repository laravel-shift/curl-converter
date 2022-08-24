<?php

namespace App\Support;

class HttpCall
{
    public static function format(\App\Models\Request $request)
    {
        // TODO:
        return 'Http::get(' . $request->url() . ');';
    }
}
