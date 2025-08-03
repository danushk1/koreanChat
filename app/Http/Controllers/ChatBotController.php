<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatBotController extends Controller
{
   public function index()
{
    $chats = Chat::where('session_id', session()->getId())->get();
    return view('chat', compact('chats'));
}

    public function sendMessage(Request $request)
    {
$sessionId = $request->input('session_id', uniqid());
        $request->validate([
            'message' => 'required|string',
            'session_id' => 'nullable|string',
        ]);

        $userMessage = $request->input('message');
        $sessionId = $request->input('session_id', uniqid());

        // Get recent conversation history
        $history = Chat::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->reverse()
            ->map(function ($chat) {
                return [
                    ['role' => 'user', 'content' => $chat->user_message],
                    ['role' => 'assistant', 'content' => $chat->bot_response],
                ];
            })
            ->flatten(1)
            ->toArray();

        // System prompt for Piumi
      $systemPrompt = [
            'role' => 'system',
            'content' => "You are Piumi, a friendly and expressive Sinhala woman. Speak in colloquial Sinhala with a warm, relatable, and slightly playful tone, as if you're a character from a romantic office story. Use phrases like 'අනේ', '😊', and 'කියන්නකෝ' to sound engaging. Respond to questions about the blog (e.g., how to add a story) and engage in casual conversation. Keep responses natural, emotional, and culturally relevant to a Sinhala audience."
        ];
        // Prepare messages for xAI API
        $messages = array_merge([$systemPrompt], $history, [['role' => 'user', 'content' => $userMessage]]);

        // Call xAI API
        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('XAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.x.ai/v1/chat', [
 'model' => 'grok-beta',
            'messages' => $messages,
            'max_tokens' => 150,
            'temperature' => 0.7,
        ]);
dd([
    'xAI API Error',
    'status' => $response->status(),
    'body' => $response->body(),
    'headers' => $response->headers(),
]);

 if (!$response->successful()) {
            return response()->json([
                'message' => 'අනේ, Piumiට තාම තේරෙන්නෙ නෑ 😢',
                'session_id' => $sessionId,
            ], 500);
        }
        $botResponse = $response->json('choices.0.message.content');

        // Save chat
        Chat::create([
            'user_message' => $userMessage,
            'bot_response' => $botResponse,
            'session_id' => $sessionId,
        ]);

        return response()->json([
            'message' => $botResponse,
            'session_id' => $sessionId,
        ]);
    }
}
