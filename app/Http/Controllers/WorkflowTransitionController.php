<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\WorkflowTransitionRepositoryInterface;


class WorkflowTransitionController extends Controller
{
    private $workflowTransitionRepositoryInterface;

    public function __construct(WorkflowTransitionRepositoryInterface $workflowTransitionRepositoryInterface)
    {
        $this->workflowTransitionRepositoryInterface = $workflowTransitionRepositoryInterface;
    }

    public function get($id)
    {
        return response()->json($this->workflowTransitionRepositoryInterface->getWorkflowTransitions($id));
    }

    public function update(Request $request, $id)
    {
        return $this->workflowTransitionRepositoryInterface->updateWorkflowTransition($id, $request->all());
    }

    public function createWorkFlowTransition(Request $request)
    {
        return $this->workflowTransitionRepositoryInterface->createWorkFlowTransition($request->all());
    }
}
