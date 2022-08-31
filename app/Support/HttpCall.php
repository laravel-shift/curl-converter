<?php

namespace App\Support;

use App\Models\Request;
use Illuminate\Support\Str;
use Symfony\Component\VarExporter\VarExporter;

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

    private static function collapseHelpers(array $headers, array &$options): array
    {
        return collect($headers)
            ->reject(function ($value, $header) use (&$options) {
                if ($header === 'Accept' && Str::lower($value) === 'application/json') {
                    $options[] = 'acceptJson()';

                    return true;
                }

                if ($header === 'Authorization' && Str::of($value)->lower()->startsWith('bearer ')) {
                    $options[] = 'withToken(\'' . substr($value, 7) . '\')';

                    return true;
                }

                return false;
            })
            ->all();
    }

    private static function filterHeaders(Request $request): array
    {
        return collect($request->headers())
            ->reject(function ($value, $header) use ($request) {
                if ($request->data() && $header === 'Content-Type') {
                    if ($request->isMultipartFormData() && Str::lower($value) === 'multipart/form-data') {
                        return true;
                    } elseif (Str::lower($value) === 'application/x-www-form-urlencoded') {
                        return true;
                    }
                }

                if ($header === 'Content-Type' && Str::lower($value) === 'application/json') {
                    return true;
                }

                return false;
            })
            ->all();
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

        $headers = self::filterHeaders($request);
        $headers = self::collapseHelpers($headers, $options);

        if ($request->hasUsernameOrPassword()) {
            $options[] = 'withBasicAuth(\'' . $request->username() . '\', \'' . $request->password() . '\')';
        }

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

    private static function prettyPrintArray(array $data)
    {
        return trim(preg_replace('/^/m', '    ', VarExporter::export($data)));
    }
}
