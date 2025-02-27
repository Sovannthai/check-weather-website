<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = DB::table('chats')->get();

        if ($request->filled('title')) {
            return response()->stream(function () use ($request) {
                try {
                    $userMessage = $request->title;
                    $botResponse = "";

                    $stream = Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'user', 'content' => $userMessage]
                        ],
                        'stream' => true,
                    ])->body();

                    if ($stream) {
                        foreach (explode("\n", $stream) as $chunk) {
                            if (!empty($chunk) && str_starts_with($chunk, "data: ")) {
                                $data = json_decode(substr($chunk, 6), true);
                                if (isset($data['choices'][0]['delta']['content'])) {
                                    $botResponse .= $data['choices'][0]['delta']['content'];

                                    // Send response chunk to frontend
                                    echo $data['choices'][0]['delta']['content'];
                                    ob_flush();
                                    flush();
                                }
                            }
                        }
                    }

                    // Save the chat history after completion
                    DB::table('chats')->insert([
                        'user_message' => $userMessage,
                        'bot_response' => $botResponse,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        }

        return view('chat', compact('chats'));
    }
    public function sendChat(Request $request)
    {
        if ($request->filled('title')) {
            return response()->stream(function () use ($request) {
                try {
                    $userMessage = $request->title;
                    $botResponse = "";

                    $stream = Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                        'Content-Type' => 'application/json',
                    ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'user', 'content' => $userMessage]
                        ],
                        'stream' => true,
                    ])->body();

                    if ($stream) {
                        foreach (explode("\n", $stream) as $chunk) {
                            if (!empty($chunk) && str_starts_with($chunk, "data: ")) {
                                $data = json_decode(substr($chunk, 6), true);
                                if (isset($data['choices'][0]['delta']['content'])) {
                                    $botResponse .= $data['choices'][0]['delta']['content'];

                                    // Send streamed response
                                    echo "data: " . $data['choices'][0]['delta']['content'] . "\n\n";
                                    ob_flush();
                                    flush();
                                }
                            }
                        }
                    }

                    // Save chat history after streaming is complete
                    DB::table('chats')->insert([
                        'user_message' => $userMessage,
                        'bot_response' => $botResponse,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    echo "data: Error: " . $e->getMessage() . "\n\n";
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        }
    }
}
