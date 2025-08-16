<?php

namespace App\Http\Controllers;

use App\Models\OpenAIDocuments;
use App\Repositories\Contracts\OpenAIDocumentRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\OpenAIStreamService;

class OpenAIController extends Controller
{

    protected $openAIDocumentRepository;
    protected $queryString;
    protected $openAI;

    public function __construct(OpenAIStreamService $openAI, OpenAIDocumentRepositoryInterface $openAIDocumentRepository)
    {
        $this->openAI = $openAI;
        $this->openAIDocumentRepository = $openAIDocumentRepository;
    }

    public function getOpenAiDocuments(Request $request)
    {
        $queryString = (object) $request->all();
        $count = $this->openAIDocumentRepository->getOpenAiDocumentsCount($queryString);
        return response()->json($this->openAIDocumentRepository->getOpenAiDocuments($queryString))
            ->withHeaders(['totalCount' => $count, 'pageSize' => $queryString->pageSize, 'skip' => $queryString->skip]);
    }

    public function delete($id)
    {
        return OpenAIDocuments::findOrFail($id)->delete();
    }

    public function getOpenApiDocumentReponse($id)
    {
        return $this->openAIDocumentRepository->getOpenAiDocumentsResponse($id);
    }

    public function stream(Request $request)
    {
        try {
            if (env('OPENAI_API_KEY') == null || env('OPENAI_API_KEY') == '') {
                return response()->json([
                    'message' => 'OpenAI API key is not set.',
                ], 404);
            }

            $request->validate(['promptInput' => 'required|string']);
            $prompt = $this->buildPrompt($request->all());
            $selectedModel = $request['selectedModel'] ?? 'gpt-4';
            $stream = $this->openAI->streamChat($prompt, $selectedModel);
            $buffer = '';

            return response()->stream(function () use ($stream, $prompt, $buffer, $request) {
                foreach ($stream as $response) {
                    $content = $response->choices[0]->delta->content ?? '';

                    $buffer .= $content;

                    echo "data: " . json_encode(['text' => $content]) . "\n\n";
                    ob_flush();
                    flush();
                }

                $this->saveAIDocument($prompt, $buffer, $request);

                echo "data: " . json_encode(['text' => '##[[DONE]]##']) . "\n\n";
                ob_flush();
                flush();
                usleep(100); // 500
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error while generating content',
            ], 404);
        }
    }

    public function saveAIDocument($prompt, $response, $request)
    {
        OpenAIDocuments::create([
            'prompt' => $prompt,
            'response' => $response,
            'model' => $$request['selectedModel'] ?? 'gpt-4',
            'language' => $request['language'] ?? null,
            'creativity' => $request['creativity'] ?? null,
            'maximumLength' => $request['maximumLength'] ?? null,
            'toneOfVoice' => $request['toneOfVoice'] ?? null
        ]);
    }

    public function buildPrompt($request)
    {
        $prompt = $request['promptInput'];

        if ($request['language']) {
            $prompt =  $prompt . ' Language is ' . $request['language'];
        }

        if ($request['creativity'] && (float)$request['creativity'] > 0) {
            $prompt =  $prompt . ' Creativity level is ' . $request['creativity'] . ' between 0 and 1';
        }

        if ($request['maximumLength'] && (int)$request['maximumLength'] > 0) {
            $prompt =  $prompt . ' Maximum ' . $request['maximumLength'] . ' words';
        }

        if ($request['toneOfVoice']) {
            $prompt =  $prompt . ' Tone of voice must be ' . $request['toneOfVoice'];
        }

        return $prompt;
    }
}
