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

        if (!empty($request->data()) && $request->method() !== 'GET') {
            if ($request->isMultipartFormData()) {
                $options[] = 'asMultipart()';
            } elseif ($request->isJsonData()) {
                $options[] = 'withBody(\'' . $request->data()[0] . '\')';
            } else {
                $options[] = 'asForm()';
            }
        }

        // TODO: filter out headers that have Http helper methods, for example: `acceptJson`
        $headers = $request->headers();

        if ($headers) {
            $options[] = 'withHeaders(' . self::prettyPrintArray($headers) . ')';
        }

        if (empty($options)) {
            return '';
        }

        return implode(PHP_EOL . '    ->', $options) . PHP_EOL . '    ->';
    }

    private static function generateRequest(Request $request): string
    {
        if (empty($request->data()) || $request->isJsonData()) {
            return "'" . $request->url() . "'";
        }

        return sprintf('\'%s\', %s', $request->url(), self::prettyPrintArray($request->data()));
    }

    private static function prettyPrintArray(array $data, $assoc = true)
    {
        $output = var_export($data, true);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        $output = preg_replace('/^\s+/m', '        ', $output);
        $output = preg_replace(['/^array \(/', '/\)$/'], ['[', '    ]'], $output);

        if (!$assoc) {
            $output = preg_replace('/^(\s+)[^=]+=>\s+/m', '$1', $output);
        }

        return trim(str_replace("\n", PHP_EOL, $output));
    }
}
