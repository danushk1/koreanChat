<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;

class PaymentController extends Controller
{
     public function webhook(Request $request)
    {
        $fingerprint = $request->input('custom_1');
        $email = $request->input('custom_2');
        $user = User::where('email', $email)->first();

        Subscription::create([
            'user_id' => $user?->id,
            'fingerprint_id' => $fingerprint,
            'start_date' => now(),
            'end_date' => now()->addMonths(2),
            'is_active' => true,
        ]);

        return response()->json(['message' => 'Subscription activated']);
    }
}
