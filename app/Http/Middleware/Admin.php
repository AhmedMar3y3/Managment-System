<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            logger('admin middleware: No authenticated admin.');
            return response()->json(['message' => 'غير مصرح: يمكن فقط للمسؤولين الوصول إلى هذا المسار'], 403);
        }
    
        logger('admin middleware: admin authenticated.', ['admin' => $admin]);
        return $next($request);
    }
}
