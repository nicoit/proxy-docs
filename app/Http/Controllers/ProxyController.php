<?php

namespace App\Http\Controllers;

use Proxy\Proxy;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Proxy\Filter\RemoveEncodingFilter;
use Laminas\Diactoros\ServerRequestFactory;
use GuzzleHttp;
use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public function handle (Request $request) {
        $client = new GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://docs.google.com',
            // You can set any number of default request options.
            'timeout'  => 200.0,
        ]);
        $response = $client->request('GET', $request->getRequestUri());
        dd($request->getRequestUri(), $response->getHeaders(), $response->getBody()->getContents());
        return $response->getBody();
    }

    public function handle1 () {


// Create a PSR7 request based on the current browser request.
        $request = ServerRequestFactory::fromGlobals(['https://docs.google.com']);

// Create a guzzle client
        $guzzle = new GuzzleHttp\Client();

// Create the proxy instance
        $proxy = new Proxy(new GuzzleAdapter($guzzle));

// Add a response filter that removes the encoding headers.
        $proxy->filter(new RemoveEncodingFilter());

// Forward the request and get the response.
        //$request['headers']['host'] = 'https://docs.google.com';
        //dd($request->getAttributes(), $request);
        $response = $proxy->forward($request)->to('https://docs.google.com');

// Output response to the browser.
        (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
    }
}
