<?php

namespace Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
use Tests\TestCase;

class CurlCommandTest extends TestCase
{
    /**
     * @test
     * @dataProvider curlCommandFixtures
     */
    public function it_converts_curl_requests_to_http_client_code($fixture)
    {
        $code = Artisan::call('shift:' . $this->fixture($fixture . '.in'));
        $output = trim(Artisan::output());

        $this->assertSame(0, $code);
        $this->assertSame($this->fixture($fixture . '.out'), $output);
    }

    public function test_it_throw_exception_when_for_invalid_url()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "https://{domain:port}/api/{id}/" URL is invalid.');

        Artisan::call('shift:curl -X GET "https://{domain:port}/api/{id}/"');
    }

    public function test_it_throw_exception_when_for_invalid_headers()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "foo" header  must be key/value pair separated by ":".');

        Artisan::call("shift:curl https://example.com --header 'foo'");
    }

    public function curlCommandFixtures()
    {
        return [
            'GET request' => ['basic-get'],
            'POST request' => ['basic-post'],
            'POST request with data' => ['post-with-data'],
            'POST request with JSON data' => ['post-json'],
            'POST request with multipart/form-data' => ['post-with-form-data'],
            'Request with data defaults to POST' => ['request-with-data'],
            'Request with form fields defaults to POST' => ['request-with-form-data'],
            'Request with collapsable headers' => ['with-collapsable-headers'],
            'PUT request with data' => ['put-with-data'],
            'GET request with headers' => ['with-headers'],
            'GET request with query string' => ['with-query-string'],
            'Mailgun example request' => ['mailgun-example'],
            'Digital Ocean example request' => ['digital-ocean-example'],
            'Stripe example request' => ['stripe-example'],
            'Stripe query params' => ['stripe-query-params'],
            'Initial connection timeout' => ['connect-timeout'],
            'Entire transaction timeout' => ['max-timeout'],
            'Ignore location flag' => ['ignore-location-flag'],
            'Missing URL scheme' => ['missing-url-scheme'],
            'GET request with compressed flag' => ['with-compressed-option'],
            'GET request with insecure flag' => ['with-insecure-option'],
            'Request with raw data' => ['with-raw-data'],
            'POST request with mixed data' => ['raw-data-mixed'],
        ];
    }

    private function fixture(string $fixture)
    {
        return trim(file_get_contents('tests/fixtures/' . $fixture));
    }
}
