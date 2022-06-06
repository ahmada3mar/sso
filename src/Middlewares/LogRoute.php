<?php

namespace Hyperpay\ConnectIn\Middlewares;

use Hyperpay\ConnectIn\Models\MongoLog;
use Closure;
use Illuminate\Http\Request;

class LogRoute
{
    /**
     * Handle an incoming request.
     * And store all requests and response
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
        $response =  $next($request);

        if ($response->exception) {
            $ex =  $response->exception->getMessage();
        }

        MongoLog::firstOrCreate([
            'id' => $request['customParameters']['UUID'],
            'authentication_entityId' => $request['authentication_entityId'],
            ])
            ->push('ACI', [
                'request' => [
                    'URL' => $request->getUri(),
                    'data' => $request->all()
                ],
                'response' => $ex ??  $response->original
            ]);


        return  $response;
    }
}
