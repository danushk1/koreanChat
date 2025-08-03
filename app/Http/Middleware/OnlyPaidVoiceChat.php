<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class OnlyPaidVoiceChat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
       
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'You must be logged in to access this feature.'
            ], 401);
        }

        $hasValidSub = Subscription::where(function ($q) use ($user) {
            
            $q->where('user_id', optional($user)->id)
              ->where('is_active', true);
              
        })->exists();

        if (!$hasValidSub) {
            return response()->json([
                'message' => 'Voice chat is only available for paid users.'
            ], 403);
        }

        return $next($request);
    }
}
