<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next){
        
            if (auth()->user() && auth()->user()->role === 'admin') {
                return $next($request);
            }
            if (auth()->user() && auth()->user()->role === 'manager') {
                return $next($request);
            }
            return response()->json(['message' => 'Unauthorized'], 403);
        }
}
