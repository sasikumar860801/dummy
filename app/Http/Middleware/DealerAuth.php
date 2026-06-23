<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
class DealerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = str_replace(
            'Bearer ',
            '',
            $request->header('Authorization', '')
        );

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token required'
            ], 401);
        }

        $dealer = DB::table('dealers')
            ->where('auth_token', $token)
            ->where('status', 1)
            ->first();

        if (!$dealer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized dealer'
            ], 401);
        }

        // Make dealer available everywhere
        $request->merge([
            'dealer_id' => $dealer->id
        ]);

        $request->attributes->set('dealer', $dealer);

        return $next($request);
    }
}
