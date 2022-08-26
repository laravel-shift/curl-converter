<?php

namespace App\Support;

use App\Models\Request;
use Illuminate\Support\Str;

class HttpCall
{
    public static function format(Request $request): string
    {
        return sprintf(
            'Http::%s%s(%s);',
            self::generateOptions($request),
            Str::lower($request->method()),
            self::generateRequest($request),
        );
    }

    private static function generateOptions(Request $request): string
    {
        $options = [];

        if ($request->headers()) {
            // TODO: what about headers that have Http helper methods, for example: `acceptJson`
            $options[] = 'withHeaders(' . var_export($request->headers(), true) . ')';
        }

        if (empty($options)) {
            return '';
        }

        return implode(PHP_EOL . '    ->', $options) . PHP_EOL . '    ->';
    }

    private static function generateRequest(Request $request): string
    {
        if (empty($request->data())) {
            return "'" . $request->url() . "'";
        }

        return sprintf('\'%s\', %s', $request->url(), var_export($request->data(), true));
    }
}
