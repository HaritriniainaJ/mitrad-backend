<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->header('Origin');
        $allowed = ['http://localhost:5173', 'http://localhost:8080', 'https://projournalmitrad.vercel.app'];

        if (in_array($origin, $allowed) || str_ends_with($origin ?? '', '.vercel.app')) {
            if ($request->isMethod('OPTIONS')) {
                return response('', 200)
                    ->header('Access-Control-Allow-Origin', $origin)
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept')
                    ->header('Access-Control-Allow-Credentials', 'true');
            }
            return $next($request)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept')
                ->header('Access-Control-Allow-Credentials', 'true');
        }
        return $next($request);
    }
}