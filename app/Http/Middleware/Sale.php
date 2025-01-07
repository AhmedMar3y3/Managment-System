<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Sale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sale = Auth::guard('sale')->user();
        
        if (!$sale) {
            logger('sale middleware: No authenticated sale.', ['token' => $request->bearerToken()]);
            return response()->json(['message' => 'غير مصرح: يمكن فقط للسيلز الوصول إلى هذا المسار'], 403);
        }
    
        logger('sale middleware: sale authenticated.', ['sale' => $sale]);
        return $next($request);
    }
}
