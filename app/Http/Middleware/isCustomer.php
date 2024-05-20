<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next){

        if (auth()->user() && auth()->user()->role === 'customer') {
            return $next($request);
        }
        
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
