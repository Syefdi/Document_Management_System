<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\WorkflowLogRepositoryInterface;

class WorkflowLogController extends Controller
{
    private $workflowRepository;

    public function __construct(WorkflowLogRepositoryInterface $workflowRepository)
    {
        $this->workflowRepository = $workflowRepository;
    }

    public function getWorkflowLogs(Request $request)
    {
        $queryString = (object) $request->all();

        $count = $this->workflowRepository->getWorkflowLogCount($queryString);
        return response()->json($this->workflowRepository->getWorkflowLogs($queryString))
            ->withHeaders(['totalCount' => $count, 'pageSize' => $queryString->pageSize, 'skip' => $queryString->skip]);
    }
}
