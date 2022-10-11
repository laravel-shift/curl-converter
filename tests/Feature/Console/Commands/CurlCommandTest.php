<?php

namespace Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Artisan;
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

    /**
     * @test
     *
     * The "--location" flag tells cURL to follow any redirects.
     * This is the default behaviour for Laravel's Http client so it can be safely ignored.
     */
    public function the_location_curl_flag_can_be_ignored()
    {
        $this->artisan('shift:curl --location http://laravel.com')->assertSuccessful();
        $this->artisan('shift:curl -L http://laravel.com')->assertSuccessful();
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
        ];
    }

    private function fixture(string $fixture)
    {
        return trim(file_get_contents('tests/fixtures/' . $fixture));
    }
}
