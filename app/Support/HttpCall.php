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
            $options[] = 'withHeaders(' . self::prettyPrintArray($request->headers()) . ')';
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

        return sprintf('\'%s\', %s', $request->url(), self::prettyPrintArray($request->data()));
    }

    private static function prettyPrintArray(array $data, $assoc = true)
    {
        $output = var_export($data, true);
        $output = preg_replace('/^\s+/m', '    ', $output);
        $output = preg_replace(['/^array\s\(/', '/\)$/'], ['[', '    ]'], $output);

        if (!$assoc) {
            $output = preg_replace('/^(\s+)[^=]+=>\s+/m', '$1', $output);
        }

        return trim(str_replace("\n", PHP_EOL, $output));
    }
}
