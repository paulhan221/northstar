<?php namespace Northstar\Http\Middleware;

use Northstar\Models\Token;
use Closure;
use Request;

class AuthenticateToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = Request::header('Session');
        if (!$token) {
            return Response::json("No token found.");
        }

        if (!Token::where('key', '=', $token)->exists()) {
            return Response::json("Token mismatched.");
        }

        return $next($request);
    }

}
