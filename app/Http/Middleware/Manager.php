<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Manager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $manager = Auth::guard('manager')->user();
        
        if (!$manager) {
            logger('manager middleware: No authenticated manager.');
            return response()->json(['message' => 'غير مصرح: يمكن فقط للمسؤولين الوصول إلى هذا المسار'], 403);
        }
    
        logger('manager middleware: manager authenticated.', ['manager' => $manager]);
        return $next($request);
    }
}
