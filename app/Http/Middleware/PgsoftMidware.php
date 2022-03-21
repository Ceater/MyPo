<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PgsoftMidware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('app.requests', [
            'request' => $request->all(),
            'url' => $request->url(),
        ]);

        $validator = Validator::make($request->all(), [
            'operator_token'    => 'required',
            'secret_key'        => 'required',
        ]);

        if($validator->fails()){
            abort(401);
            Log::info('Abort');
        }
        return $next($request);
    }
}
