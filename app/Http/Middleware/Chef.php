<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Chef
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $chef = Auth::guard('chef')->user();
        
        if (!$chef) {
            logger('chef middleware: No authenticated chef.');
            return response()->json(['message' => 'غير مصرح: يمكن فقط للمسؤولين الوصول إلى هذا المسار'], 403);
        }
    
        logger('chef middleware: chef authenticated.', ['chef' => $chef]);
        return $next($request);
    }
}
