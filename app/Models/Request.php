<?php

namespace App\Models;

class Request
{
    private string $url;

    private string $method;

    private array $headers = [];

    private array $data = [];

    private bool $multipartFormData = false;

    private function __construct($url, $method)
    {
        $this->url = $url;
        $this->method = strtoupper($method);
    }

    public static function create(array $data): self
    {
        $request = new self($data['url'], $data['method']);

        if (!empty($data['headers'])) {
            $request->headers = $data['headers'];
        }

        if (!empty($data['data'])) {
            // TODO: handle JSON payload
            parse_str(implode('&', $data['data']), $request->data);
        }

        if (!empty($data['fields'])) {
            $request->data = $data['fields'];
            $request->multipartFormData = true;
        }

        return $request;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function isMultipartFormData()
    {
        return $this->multipartFormData;
    }
}
