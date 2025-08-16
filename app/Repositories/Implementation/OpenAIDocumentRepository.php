<?php

namespace App\Repositories\Implementation;

use App\Models\OpenAIDocuments;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\OpenAIDocumentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OpenAIDocumentRepository extends BaseRepository implements OpenAIDocumentRepositoryInterface
{

    /**
     * @var Model
     */
    protected $model;

    public static function model()
    {
        return OpenAIDocuments::class;
    }

    public function getOpenAiDocuments($attributes)
    {


        $query = OpenAIDocuments::select([
            'openaiDocuments.id',
            'openaiDocuments.prompt',
            'openaiDocuments.model',
            'openaiDocuments.created_at',
            'openaiDocuments.language',
            'openaiDocuments.maximumLength',
            'openaiDocuments.creativity',
            'openaiDocuments.toneOfVoice'
        ]);

        $orderByArray =  explode(' ', $attributes->orderBy);
        $orderBy = $orderByArray[0];
        $direction = $orderByArray[1] ?? 'asc';

        if ($orderBy == 'prompt') {
            $query = $query->orderBy('openaiDocuments.prompt', $direction);
        } else if ($orderBy == 'model') {
            $query = $query->orderBy('openaiDocuments.model', $direction);
        } else if ($orderBy == 'language') {
            $query = $query->orderBy('openaiDocuments.language', $direction);
        } else if ($orderBy == 'maximumLength') {
            $query = $query->orderBy('openaiDocuments.maximumLength', $direction);
        } else if ($orderBy == 'creativity') {
            $query = $query->orderBy('openaiDocuments.creativity', $direction);
        } else if ($orderBy == 'toneOfVoice') {
            $query = $query->orderBy('openaiDocuments.toneOfVoice', $direction);
        } else {
            $query = $query->orderBy('openaiDocuments.created_at', $direction);
        }

        if ($attributes->prompt) {
            $query = $query->where('prompt',  'like', '%' . $attributes->prompt . '%');
        }

        $results = $query->skip($attributes->skip)->take($attributes->pageSize)->get();

        return $results;
    }

    public function getOpenAiDocumentsCount($attributes)
    {
        $query = OpenAIDocuments::query();

        if ($attributes->prompt) {
            $query = $query->where('prompt',  'like', '%' . $attributes->prompt . '%');
        }

        $count = $query->count();
        return $count;
    }

    public function getOpenAiDocumentsResponse($id)
    {
        $model = $this->model->findOrFail($id);
        return $model;
    }
}
