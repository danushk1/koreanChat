<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class LessonController extends Controller
{

public function generateVoice(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'text' => 'required|string|max:1000',
            'voice' => 'nullable|string|in:nova,alloy,fable', // Adjust voices if xAI uses different options
        ]);

        $text = $validated['text'] ?? 'Hello! Welcome to your English lesson.';
        $voice = $validated['voice'] ?? 'nova';

        // Check for API key
        if (!env('XAI_API_KEY')) {
            Log::error('XAI_API_KEY is not set in .env');
            return response()->json(['error' => 'API key is missing'], 500);
        }

        try {
            // Replace with the correct xAI TTS endpoint after verifying with xAI documentation
            // Current endpoint (https://api.x.ai/v1/audio/speech) returns 404
            $endpoint = 'https://api.x.ai/v1/audio/speech'; // TODO: Confirm correct endpoint via https://x.ai/api or xAI support

            // Make API request
            $response = Http::retry(3, 2000)
                ->timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('XAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, [
                    'model' => 'tts-1', // Adjust model if xAI uses a different one
                    'voice' => $voice,
                    'input' => $text,
                ]);

            // Check if request was successful
            if (!$response->successful()) {
                $errorBody = $response->json();
                Log::error('xAI TTS API request failed', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                    'headers' => $response->headers(),
                ]);
                if ($response->status() === 404) {
                    return response()->json([
                        'error' => 'TTS endpoint not found',
                        'details' => 'Please verify the xAI API endpoint at https://x.ai/api'
                    ], 404);
                }
                return response()->json([
                    'error' => 'Failed to generate audio',
                    'status' => $response->status(),
                    'details' => $errorBody['error']['message'] ?? 'Unknown error',
                ], 500);
            }

            // Validate content type
            if ($response->header('Content-Type') !== 'audio/mpeg') {
                Log::error('Unexpected response content type', [
                    'content_type' => $response->header('Content-Type'),
                ]);
                return response()->json(['error' => 'Invalid audio response'], 500);
            }

            // Store the audio file
            $filename = 'spoken-lesson-' . time() . '.mp3';
            $path = 'public/' . $filename;

            Storage::makeDirectory('public');
            Storage::put($path, $response->body());

            // Return downloadable file
            return response()->download(
                storage_path('app/' . $path),
                'english-lesson.mp3'
            )->deleteFileAfterSend(true);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Network error contacting xAI TTS API', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Network error occurred', 'details' => $e->getMessage()], 503);
        } catch (\Exception $e) {
            Log::error('Unexpected error generating voice audio', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }
}

