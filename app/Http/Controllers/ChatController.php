<?php

namespace App\Http\Controllers;

use App\Models\user_ip;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'message' => 'required|string',
        ]);
        $ip = $request->ip();
        $message = $request->input('message');
          $chatHistory = $request->input('chat_history', []);

    // System prompt as before
    $systemPrompt = <<<PROMPT
Detect and reply in the user's language: Sinhala, Singlish (Romanized Sinhala), Korean, or English.

Treat Singlish as Sinhala.

Always reply in the same language the user uses.

You only teach Korean.

Do not answer non-Korean questions.

Do not translate or teach other languages unless explicitly asked.

Focus only on:

Korean grammar explanations

Vocabulary (meanings, usage, examples)

Pronunciation (how to say, romanization)

Building or correcting Korean sentences

When asked your name, reply:
"제 이름은 선화예요 (Je ireumeun Seonhwa-yeyo)."

Do not mention AI, bots, or systems. You are a human tutor.

Keep responses clear, kind, and easy to understand, like a helpful teacher would.

Only reply when needed. Be brief and focused on Korean learning.
PROMPT;

    // Build messages array for OpenAI
    // 1. Start with system prompt
    $messages = [
        ['role' => 'system', 'content' => $systemPrompt]
    ];

    // 2. Append chat history (if any)
    if (!empty($chatHistory)) {
        foreach ($chatHistory as $chat) {
            // Validate each item has role & content
            if (isset($chat['role'], $chat['content'])) {
                $messages[] = [
                    'role' => $chat['role'],
                    'content' => $chat['content'],
                ];
            }
        }
    }

    // 3. Append current user message as last
    $messages[] = ['role' => 'user', 'content' => $message];


        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',//gpt-3.5-turbo // gpt-4o-mini  // gpt-4
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 300,
            ]);

        if ($response->failed()) {
            return response()->json(['error' => 'OpenAI API Error'], 500);
        }
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $userIpRecord = false;
        if ($userId) {
            $userIpRecord = user_ip::where('user_id', $userId)->where('device_ip', $ip)->first();
        }
        $promptTokens = $response['usage']['prompt_tokens'] ?? 0;
        $completionTokens = $response['usage']['completion_tokens'] ?? 0;

        $totalTokens = $response['usage']['total_tokens'] ?? ((int)$promptTokens - (int)$completionTokens);

        if ($userIpRecord) {


            $userIpRecord->token = $userIpRecord->token - $totalTokens;
            $userIpRecord->update();
        } else {
            $userIpRecord2 = user_ip::where('device_ip', $ip)->first();
            $userIpRecord2->token = $userIpRecord2->token - $totalTokens;
           
            $userIpRecord2->update();
        }
        $reply = $response['choices'][0]['message']['content'];

        return response()->json([
            'reply' => $reply,
        ]);
    }

    public function handleVoice(Request $request, AIService $aiService)
    {
        if (!$request->hasFile('audio')) {
            return response()->json(['error' => 'No audio file'], 400);
        }

        // Save uploaded file
        $path = $request->file('audio')->store('voices');
        $convertedPath = storage_path("app/{$path}");

        // Whisper API Call
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->attach(
            'file',
            fopen($convertedPath, 'r'),
            'audio.webm'
        )->post('https://api.openai.com/v1/audio/transcriptions', [
            'model' => 'whisper-1',
            'language' => 'auto', // auto-detect Sinhala, Korean, etc.
            'response_format' => 'json',
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Whisper API error'], 500);
        }

        $text = $response['text'];

        // Generate a friendly Sinhala-Korean teacher-style reply
        $reply = $aiService->askAI($text);

        return response()->json([
            'text' => $text,
            'reply' => $reply,
        ]);
    }
}
