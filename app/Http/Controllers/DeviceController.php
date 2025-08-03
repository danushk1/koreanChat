<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function authenticate(Request $request)
    {
        $deviceId = $request->input('device_id');

        if (!$deviceId) {
            return response()->json(['message' => 'Missing device ID'], 400);
        }

        $today = now()->toDateString();
        $token = hash('sha256', $deviceId . $today); // unique per day

        // Only check if token for today already exists
        $record = DB::table('devices')
            ->where('device_id', $deviceId)
            ->where('date', $today)
            ->first();

        if (!$record) {
            DB::table('devices')->insert([
                'device_id' => $deviceId,
                'secret_token' => $token,
                'usage_count' => 0,
                'date' => $today,
                'created_at' => now(), // Optional if your table supports timestamps
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'secret_token' => $token,
        ]);
    }
}
