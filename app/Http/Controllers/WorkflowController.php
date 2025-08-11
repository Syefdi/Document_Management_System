<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Contracts\WorkflowRepositoryInterface;


class WorkflowController extends Controller
{
    private $workflowRepositoryInterface;

    public function __construct(WorkflowRepositoryInterface $workflowRepositoryInterface)
    {
        $this->workflowRepositoryInterface = $workflowRepositoryInterface;
    }

    public function getAll()
    {
        return response()->json($this->workflowRepositoryInterface->getWorkflows());
    }

    public function get($id)
    {
        return response()->json($this->workflowRepositoryInterface->getWorkflow($id));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|unique:workflows,name,NULL,id,deleted_at,NULL",
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }

        return response()->json($this->workflowRepositoryInterface->createWorkflow($request->all()));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|unique:workflows,name,$id,id,deleted_at,NULL",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }

        return response()->json($this->workflowRepositoryInterface->updateWorkflow($id, $request->all()));
    }

    public function delete($id)
    {
        return $this->workflowRepositoryInterface->deleteWorkflow($id);
    }

    public function visualWorkflow($id)
    {
        return $this->workflowRepositoryInterface->visualWorkflow($id);
    }

    public function getMyWorkflow()
    {
        return response()->json($this->workflowRepositoryInterface->getMyWorkflow());
    }
}
