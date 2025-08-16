<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\AllowFileExtensionRepositoryInterface;

class AllowFileExtensionController extends Controller
{
    private $allowFileExtensionRepository;

    public function __construct(AllowFileExtensionRepositoryInterface $allowFileExtensionRepository)
    {
        $this->allowFileExtensionRepository = $allowFileExtensionRepository;
    }

    public function index()
    {
        return response($this->allowFileExtensionRepository->orderBy('fileType')->all(), 200);
    }

    public function update(Request $request, $id)
    {
        return  response($this->allowFileExtensionRepository->update($request->all(), $id), 204);
    }

    public function get($id)
    {
        $fileRequest = $this->allowFileExtensionRepository->find($id);
        return response($fileRequest, 201);
    }
}
