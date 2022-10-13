<?php

namespace Shift\CurlConverter\Models;

use Illuminate\Support\Str;

class Request
{
    private string $url;

    private string $method;

    private array $headers = [];

    private array $data = [];

    private bool $rawData = false;

    private bool $multipartFormData = false;

    private ?string $username = null;

    private ?string $password = null;

    private ?int $maxTimeout = null;

    private ?int $connectTimeout = null;

    private function __construct($url, $method)
    {
        $this->url = $url;
        $this->method = Str::upper($method);
    }

    public static function create(array $data): self
    {
        $url = parse_url($data['url']);

        $request = new self(self::buildUrl($url), $data['method'] ?? 'GET');

        if (isset($url['query'])) {
            parse_str($url['query'], $request->data);
        }

        if (!empty($data['headers'])) {
            $request->headers = collect($data['headers'])
                ->mapWithKeys(function ($header) {
                    [$key, $value] = explode(':', $header, 2);

                    return [trim($key) => self::convertDataType(trim($value))];
                })
                ->all();
        }

        if (!empty($data['dataUrlEncode'])) {
            $request->data = array_merge($request->data, self::parseData($data['dataUrlEncode']));
        }

        if (!empty($data['rawData'])) {
            if (count($data['rawData']) === 1 && empty($data['data']) && empty($data['dataUrlEncode'])) {
                $request->data = $data['rawData'];
                $request->rawData = true;
            } else {
                $request->data = array_merge($request->data, self::parseData($data['rawData']));
            }
        }

        if (!empty($data['data'])) {
            if (count($data['data']) === 1 && Str::startsWith($data['data'][0], '{')) {
                $request->data = $data['data'];
                $request->rawData = true;
            } else {
                $request->data = array_merge($request->data, self::parseData($data['data']));
            }
        }

        if (!empty($data['fields'])) {
            $request->data = self::parseData($data['fields']);
            $request->multipartFormData = true;
        }

        if (is_null($data['method']) && (!empty($data['rawData']) || !empty($data['data']) || !empty($data['fields'] ))) {
            $request->method = 'POST';
        }

        if ($data['user']) {
            [$request->username, $request->password] = explode(':', $data['user'], 2);
        }

        if ($data['maxTimeout']) {
            $request->maxTimeout = $data['maxTimeout'];
        }

        if ($data['connectTimeout']) {
            $request->connectTimeout = $data['connectTimeout'];
        }

        return $request;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function hasUsernameOrPassword(): bool
    {
        return isset($this->username) || isset($this->password);
    }

    public function hasMaxTimeout(): bool
    {
        return isset($this->maxTimeout);
    }

    public function hasConnectTimeout(): bool
    {
        return isset($this->connectTimeout);
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function isRawData(): bool
    {
        return $this->rawData;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function isMultipartFormData(): bool
    {
        return $this->multipartFormData;
    }

    public function username(): string
    {
        return $this->username ?? '';
    }

    public function password(): string
    {
        return $this->password ?? '';
    }

    public function maxTimeout(): int
    {
        return $this->maxTimeout;
    }

    public function connectTimeout(): int
    {
        return $this->connectTimeout;
    }

    private static function convertDataType(string $value)
    {
        return preg_match('/^[1-9]\d*$/', $value) ? intval($value) : $value;
    }

    private static function parseData(array $data): array
    {
        parse_str(implode('&', $data), $data);
        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = self::convertDataType($value);
            }
        });

        return $data;
    }

    private static function buildUrl(array $url): string
    {
        $output = ($url['scheme'] ?? 'https') . '://' . ($url['host'] ?? '');

        if (isset($url['port'])) {
            $output .= ':' . $url['port'];
        }

        if (isset($url['path'])) {
            $output .= $url['path'];
        }

        return $output;
    }
}
