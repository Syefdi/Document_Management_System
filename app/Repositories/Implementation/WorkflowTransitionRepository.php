<?php

namespace App\Repositories\Implementation;

use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use App\Models\WorkflowTransitionRole;
use App\Models\WorkflowTransitionUser;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\WorkflowTransitionRepositoryInterface;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class WorkflowTransitionRepository.
 */
class WorkflowTransitionRepository extends BaseRepository implements WorkflowTransitionRepositoryInterface
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
        return WorkflowTransition::class;
    }

    public function getWorkflowTransitions($id)
    {
        return $this->model->with(['workflowTransitionRoles', 'workflowTransitionUsers'])
            ->where('workflowId', $id)
            ->get();
    }

    public function createWorkflowTransition($request)
    {
        $workflowId = $request[0]['workflowId'];
        if (!$workflowId) {
            return response()->json(['message' => 'Workflow ID is missing.'], 404);
        }

        $workflow = Workflow::find($workflowId);
        if (!$workflow) {
            return response()->json(['message' => 'Workflow does not exist.'], 404);
        }

        DB::beginTransaction();

        try {

            foreach ($request as $transitionData) {
                $transition =  WorkflowTransition::create([
                    'name' => $transitionData['name'],
                    'color' => $transitionData['color'],
                    'workflowId' => $transitionData['workflowId'],
                    'fromStepId' => $transitionData['fromStepId'],
                    'toStepId' => $transitionData['toStepId'],
                    'orderNo' => $transitionData['orderNo'] ?? 0,
                    'isFirstTransaction' => $transitionData['isFirstTransaction'] ?? false,
                ]);

                foreach ($transitionData['roleIds']  as $roleId) {
                    $transition->workflowTransitionRoles()->create(['roleId' => $roleId]);
                }

                foreach ($transitionData['userIds']  as $userId) {
                    $transition->workflowTransitionUsers()->create(['userId' => $userId]);
                }
            }

            $workflow->update(['isWorkflowSetup' => true]);
            DB::commit();

            $transitions = $this->getWorkflowTransitions($workflowId);

            return response($transitions, 201);
        } catch (\Exception $th) {
            return response()->json(['Message' => 'Error in saving data.' . $th->getMessage()], 409);
        }
    }

    public function updateWorkflowTransition($id, $request)
    {
        try {
            DB::beginTransaction();

            // Load existing transitions
            $workflowTransitions = WorkflowTransition::with(['workflowTransitionUsers', 'workflowTransitionRoles'])
                ->where('workflowId', $id)
                ->get();

            if ($workflowTransitions->isEmpty()) {
                return response()->json(['message' => 'Not Found'], 404);
            }

            // Index request transitions by ID
            $requestDict = collect($request)->keyBy(fn($item) => (string) $item['id']);

            $workflowTransitions->each(function ($transition) use ($requestDict) {
                $transitionId = (string) $transition->id;

                if (!$requestDict->has($transitionId)) return;

                $req = $requestDict[$transitionId];

                $transition->update([
                    'name' => $req['name'] ?? $transition->name,
                    'color' => $req['color'] ?? $transition->color,
                ]);

                // Clean up old
                $transition->workflowTransitionRoles()->forceDelete();
                $transition->workflowTransitionUsers()->forceDelete();

                // Roles
                $roleData = collect($req['roleIds'] ?? [])
                    ->filter()
                    ->map(fn($roleId) => ['roleId' => $roleId])
                    ->toArray();

                if (!empty($roleData)) {
                    $transition->workflowTransitionRoles()->createMany($roleData);
                }

                // Users
                $userData = collect($req['userIds'] ?? [])
                    ->filter()
                    ->map(fn($userId) => ['userId' => $userId])
                    ->toArray();

                if (!empty($userData)) {
                    $transition->workflowTransitionUsers()->createMany($userData);
                }
            });

            DB::commit();

            // Return updated data
            $newTransitions = $this->getWorkflowTransitions($id);
            return response($newTransitions, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error in saving data.',
                'error' => $e->getMessage()
            ], 409);
        }
    }
}
