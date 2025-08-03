<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\user_ip;
use App\Models\Subscription;

class ChatRateLimiter
{
    public function handle($request, Closure $next)
    {
        $deviceId = $request->input('device_id');
        $ip = $request->ip();
        $user = Auth::user();

        $userIpRecord = user_ip::where('device_ip', $ip)->first();


        if ($userIpRecord && $userIpRecord->user_id > 0) {
            $userId = $userIpRecord->user_id;


            $hasSubscription = Subscription::where('user_id', $userId)

                ->where('is_active', true)
                ->where('token_balance', '>', 5)
                ->exists();

            if ($hasSubscription) {
                return $next($request); // 🔓 Unlimited access for paid users
            }

            // 🆓 Free user: check remaining token count
            if ($userIpRecord->token <= 5) {
                return response()->json(['message' => 'Daily free limit reached. Please upgrade.'], 429);
            }
        } elseif ($userIpRecord) {
            if ($userIpRecord && !$userIpRecord->updated_at->isToday()) {
                $userIpRecord->token = 2000;
                $userIpRecord->update();
            }
            if ($userIpRecord->token <= 5) {
                return response()->json(['message' => 'Daily free limit reached. Please upgrade.'], 429);
            }
        } else {
            // 🌱 First time IP — Create new record with 999 tokens (1 used now)
            $userIP =   user_ip::create([
                'device_ip' => $ip,
                'user_id' => optional($user)->id || null,
                'token' => 2000,
            ]);
            return $next($request);
        }

        return $next($request);
    }
}
