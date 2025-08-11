<?php

namespace App\Http\Controllers;

use App\Models\AIPromptTemplates;
use App\Repositories\Contracts\AIPromptTemplateRepositoryInterface;
use Illuminate\Http\Request;

class AIPromptTemplateController extends Controller
{
    private $aIPromptTemplateRepository;

    public function __construct(AIPromptTemplateRepositoryInterface $aIPromptTemplateRepository)
    {
        $this->aIPromptTemplateRepository = $aIPromptTemplateRepository;
    }

    public function index()
    {
        return response($this->aIPromptTemplateRepository->orderBy('modifiedDate','desc')->all(), 200);
    }

    public function create(Request $request)
    {
        return  response($this->aIPromptTemplateRepository->create($request->all()), 201);
    }

    public function update(Request $request, $id)
    {
        echo $id;
        return  response($this->aIPromptTemplateRepository->update($request->all(), $id), 204);
    }

    public function get($id)
    {
        $fileRequest = $this->aIPromptTemplateRepository->find($id);
        return response($fileRequest, 201);
    }

    public function delete($id)
    {
        return AIPromptTemplates::findOrFail($id)->delete();
    }
}
