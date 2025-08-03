<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function askAI($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a friendly Sinhala-Korean language teacher in Sri Lanka. Respond like a helpful, funny, and respectful teacher-friend. Use simple examples when needed.'
                ],
                [
                    'role' => 'user',
                    'content' => $text
                ],
            ],
            'temperature' => 0.7,
        ]);

        if (!$response->ok()) {
            return "AI response failed.";
        }

        return $response['choices'][0]['message']['content'] ?? "No response.";
    }
}
