<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Contracts\DocumentWorkflowRepositoryInterface;


class DocumentWorkflowController extends Controller
{
    private $documentWorkflowRepository;

    public function __construct(DocumentWorkflowRepositoryInterface $documentWorkflowRepositoryInterface)
    {
        $this->documentWorkflowRepository = $documentWorkflowRepositoryInterface;
    }

    public function saveDocumentWorkFlow(Request $request)
    {
        return $this->documentWorkflowRepository->saveDocumentWorkflow($request);
    }

    public function performNextTransition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'documentId' => 'required|uuid',
            'documentWorkflowId' => 'required|uuid',
            'transitionId' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }

        return $this->documentWorkflowRepository->performNextTransition($request);
    }

    public function cancelWorkflow(Request $request, $id)
    {
        return $this->documentWorkflowRepository->cancelWorkflow($request, $id);
    }

    public function visualWorkflow($id)
    {
        return $this->documentWorkflowRepository->visualWorkflow($id);
    }

    public function getDocumentWorkFlows(Request $request)
    {
        $queryString = (object) $request->all();

        $count = $this->documentWorkflowRepository->getDocumentWorkflowCount($queryString);
        return response()->json($this->documentWorkflowRepository->getDocumentWorkFlows($queryString))
            ->withHeaders(['totalCount' => $count, 'pageSize' => $queryString->pageSize, 'skip' => $queryString->skip]);
    }
}
