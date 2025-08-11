<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\DocumentShareableLinkRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class DocumentSharebaleLinkController extends Controller
{
    private $linkRepository;

    public function __construct(DocumentShareableLinkRepositoryInterface $linkRepository)
    {
        $this->linkRepository = $linkRepository;
    }

    public function get($id)
    {
        return $this->linkRepository->getLink($id);
    }

    public function createOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'documentId'       => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }

        return $this->linkRepository->createOrUpdateLink($request);
    }

    public function delete($id)
    {
        return $this->linkRepository->deleteLink($id);
    }

    public function getLinkInfoByCode($id)
    {
        return $this->linkRepository->getLinkInfoByCode($id);
    }

    public function getDocumentByCode($id)
    {
        return $this->linkRepository->getDocumentByCode($id);
    }

    public function validatePassword($id, $password)
    {
        return $this->linkRepository->validatePassword($id, $password);
    }
}
