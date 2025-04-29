<?php

namespace App\Example;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ZeroRPC\Context;
use ZeroRPC\Client;
use ZeroRPC\Hook\ConfigMiddleware;

class ExampleController extends Controller
{
    /**
     * @throws \ZMQSocketException
     */
    public function index(Request $request)
    {

        $middleware = new ConfigMiddleware(array(
            'ZERORPC_TIME' => array(
                '1.0' => 'tcp://192.168.0.84:4242',
                'access_key' => 'testing_client_key',
                'default' => '1.0',
            ),
        ));

        $context = new Context();
        $context->registerHook('resolve_endpoint', $middleware->resolveEndpoint());
        $context->registerHook('before_send_request', $middleware->beforeSendRequest());
        $context->registerHook('after_response', function() {
            echo 'Do something after request finished' . PHP_EOL;
        });

        $client = new Client("time", '1.0', $context);

        $responses = [];
        for($i=0;$i<=10;$i++) {
            $responses[] = $client->hello("Guizao v $i");
        }

       return view('example.index', ['time' => implode('-', $responses)]);
    }
}
