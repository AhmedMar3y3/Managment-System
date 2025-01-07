<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Delivery
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $delivery = Auth::guard('delivery')->user();
        
        if (!$delivery) {
            logger('delivery middleware: No authenticated delivery.');
            return response()->json(['message' => 'غير مصرح: يمكن فقط للسائقين الوصول إلى هذا المسار'], 403);
        }
    
        logger('delivery middleware: delivery authenticated.', ['delivery' => $delivery]);
        return $next($request);
    }
}
