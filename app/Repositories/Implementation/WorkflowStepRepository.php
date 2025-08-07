<?php

namespace App\Repositories\Implementation;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\WorkflowStepRepositoryInterface;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class WorkflowStepRepository.
 */
class WorkflowStepRepository extends BaseRepository implements WorkflowStepRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public static function model()
    {
        return WorkflowStep::class;
    }

    public function getWorkflowSteps($id)
    {
        return $this->model
            ->where('workflowId', $id)
            ->get();
    }

    public function createWorkflowStep($attributes)
    {
        try {

            DB::beginTransaction();

            $states = $attributes;
            $workflowId = $attributes[0]['workflowId'];

            if (!$workflowId || !Workflow::find($workflowId)) {
                return response()->json(['message' => 'Workflow not found'], 404);
            }

            $workflowStep = WorkflowStep::where('workflowId', $workflowId)->get();
            WorkflowStep::destroy($workflowStep);

            foreach ($states as $state) {
                $workflowStep =  WorkflowStep::create([
                    'name' => $state['name'],
                    'workflowId' => $state['workflowId']
                ]);
            }
            DB::commit();
            $workflowSteps = $this->getWorkflowSteps($workflowId);
            return response($workflowSteps, 201);
        } catch (\Throwable $th) {
            return response()->json(['Message' => 'Error in saving data.' . $th->getMessage()], 409);
        }
    }

    public function updateWorkflowStep($id, $request)
    {
        $workflowSteps = WorkflowStep::where('workflowId', $id)->get();

        if ($workflowSteps->isEmpty()) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        DB::beginTransaction();

        try {

            $inputSteps = collect($request)->keyBy('id');

            foreach ($workflowSteps as $step) {
                if ($inputSteps->has($step->id)) {
                    $incoming = $inputSteps[$step->id];
                    $step->name = $incoming['name'] ?? $step->name;
                }
            }

            foreach ($workflowSteps as $step) {
                $step->save();
            }

            DB::commit();

            return response()->json($workflowSteps, 201);
        } catch (\Throwable $th) {
            return response()->json(['Message' => 'Error in saving data.' . $th->getMessage()], 409);
        }
    }
}
