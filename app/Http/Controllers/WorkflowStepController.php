<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\WorkflowStepRepositoryInterface;


class WorkflowStepController extends Controller
{
    private $workflowStepRepositoryInterface;

    public function __construct(WorkflowStepRepositoryInterface $workflowStepRepositoryInterface)
    {
        $this->workflowStepRepositoryInterface = $workflowStepRepositoryInterface;
    }

    public function get($id)
    {
        return response()->json($this->workflowStepRepositoryInterface->getWorkflowSteps($id));
    }

    public function update(Request $request, $id)
    {
        return $this->workflowStepRepositoryInterface->updateWorkflowStep($id,$request->all());
    }

    public function createWorkFlowStep(Request $request)
    {
        return $this->workflowStepRepositoryInterface->createWorkFlowStep($request->all());
    }
}
