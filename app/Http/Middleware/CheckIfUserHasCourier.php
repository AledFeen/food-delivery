<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserHasCourier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::user()) {
            $user = DB::selectOne('select * from couriers where users_id = ?', [ Auth::id()]);
            if($user) {
                return $next($request);
            } else {
                return response(['response' => false]);
            }
        }
        return response(['response' => false]);
    }
}
