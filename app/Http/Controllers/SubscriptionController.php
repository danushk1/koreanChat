<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    

    public function showForm()
    { //dd('Reached prompt method');

        return view('subscribe', [
            'plans' => [
                [
                    'name' => 'Free Plan',
                    'details' => '2000 Tokens per day',
                    'price' => 'LKR 0'
                ],
                [
                    'name' => 'Basic',
                    'details' => ' 5,000 tokens',
                    'price' => 'LKR 150'
                ],
                [
                    'name' => 'Standard',
                    'details' => ' 10,000 tokens',
                    'price' => 'LKR 250'
                ],
                [
                    'name' => 'Pro',
                    'details' => '25,000 tokens',
                    'price' => 'LKR 500'
                ],
                [
                    'name' => 'Ultimate',
                    'details' => ' 100,000 tokens',
                    'price' => 'LKR 2000'
                ]
            ]

        ]);
    }
    public function payWithPayHere(Request $request)
    {
        // Device ID from session or request (you can improve this part)
        $deviceId = session('device_id') ?? $request->input('device_id', 'unknown_device');

        $data = [
            'merchant_id' => env('PAYHERE_MERCHANT_ID'),
            'return_url' => url('/chat'),
            'cancel_url' => url('/subscribe'),
            'notify_url' => url('/subscribe/notify'),

            'order_id' => uniqid('ORDER_'),
            'items' => 'Unlimited Chat Plan',
            'amount' => '500.00', // Adjust price
            'currency' => 'LKR',

            'first_name' => 'Anonymous',
            'last_name' => '',
            'email' => 'anonymous@example.com',
            'phone' => '0000000000',
            'address' => 'N/A',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',

            'custom_1' => $deviceId,
        ];

        return view('payhere.checkout', compact('data'));
    }

    public function handlePayHereWebhook(Request $request)
    {
        // You should verify the request authenticity here for security!

        if ($request->input('status_code') == 2) { // 2 means payment success
            $deviceId = $request->input('custom_1');

            Subscription::updateOrCreate(
                ['fingerprint_id' => $deviceId],
                [
                    'start_date' => now(),
                    'end_date' => now()->addDays(30),
                    'is_active' => true,
                ]
            );

            return response('OK', 200);
        }

        return response('Payment failed or invalid', 400);
    }
}