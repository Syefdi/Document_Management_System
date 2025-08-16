<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\DocumentStatusRepositoryInterface;

class DocumentStatusController extends Controller
{
    private $documentStatusRepository;

    public function __construct(DocumentStatusRepositoryInterface $documentStatusRepository)
    {
        $this->documentStatusRepository = $documentStatusRepository;
    }

    public function index()
    {
        return response($this->documentStatusRepository->orderBy('createdDate')->all(), 200);
    }

    public function update(Request $request, $id)
    {
        return  $this->documentStatusRepository->updateStatus($request->all(), $id);
    }

    public function create(Request $request)
    {
        return  response($this->documentStatusRepository->create($request->all()), 201);
    }

    public function get($id)
    {
        $fileRequest = $this->documentStatusRepository->find($id);
        return response($fileRequest, 201);
    }

    public function delete($id)
    {
        return  response($this->documentStatusRepository->delete($id), 204);
    }
}
