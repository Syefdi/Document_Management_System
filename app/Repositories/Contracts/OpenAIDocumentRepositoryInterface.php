<?php

namespace App\Repositories\Contracts;

interface OpenAIDocumentRepositoryInterface extends BaseRepositoryInterface
{
    public function getOpenAiDocuments($attributes);
    public function getOpenAiDocumentsCount($attributes);
    public function getOpenAiDocumentsResponse($id);
}
