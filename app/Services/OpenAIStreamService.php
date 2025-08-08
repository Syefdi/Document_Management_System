<?php

namespace App\Services;

use OpenAI;

class OpenAIStreamService
{
    public function streamChat($prompt, $model)
    {
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        return $client->chat()->createStreamed([
            'model' => $model ?? 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
    }
}
