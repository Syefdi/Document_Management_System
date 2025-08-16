<?php

namespace App\Repositories\Implementation;

use App\Models\DocumentWorkflow;
use App\Models\Workflow;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\WorkflowRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


//use Your Model

/**
 * Class WorkflowRepository.
 */
class WorkflowRepository extends BaseRepository implements WorkflowRepositoryInterface
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
        return Workflow::class;
    }

    public function getWorkflows()
    {
        $workflows = $this->model->with(['workflowSteps', 'transitions'])
            ->orderBy('createdDate', 'desc')
            ->get();
        return $workflows;
    }

    public function getWorkflow($id)
    {
        $workflow = $this->model->with([
            'workflowSteps',
            'transitions' => function ($query) {
                $query->orderBy('orderNo');
            },
            'transitions.workflowTransitionRoles',
            'transitions.workflowTransitionUsers',
        ])->findOrFail($id);
        return $workflow;
    }

    public function createWorkflow($request)
    {
        try {

            $existing = Workflow::where('name', $request['name'])->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Workflow with the same name already exists.'
                ], 409);
            }

            $model = $this->model->newInstance($request);
            $model->save();
            $this->resetModel();
            $result = $this->parseResult($model);

            $result->id = (string)$result->id;
            $result['workflowSteps'] = [];
            $result['transitions'] = [];
            return $result;
        } catch (\Throwable $th) {
            return response()->json(['Message' => 'Error in saving data.' . $th->getMessage()], 409);
        }
    }

    public function updateWorkflow($id, $request)
    {
        try {

            $workflow = $this->model->findOrFail($id);

            if (!$workflow) {
                return response()->json([
                    'message' => 'Workflow does not exist.'
                ], 404);
            }

            $exists = Workflow::where('name', $request['name'])->where('id', '!=', $id)->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Workflow with same name already exists.'
                ], 409);
            }

            $workflow->name = $request['name'];
            $workflow->description = $request['description'];

            $workflow->save();
            $this->resetModel();
            $result = $this->parseResult($workflow);

            return $result;
        } catch (\Throwable $th) {
            return response()->json(['Message' => 'Error in saving data.' . $th->getMessage()], 409);
        }
    }

    public function deleteWorkflow($id)
    {
        $exists = DocumentWorkflow::query()
            ->where('workflowid', $id)
            ->exists();

        if ($exists) {
            return response()->json(['Message' => 'workflow can not be deleted as it associate with documents.'], 409);
        }

        return response($this->delete($id), 200);
    }

    public function visualWorkflow($id)
    {
        $workflow = $this->model->with([
            'workflowSteps',
            'transitions' => function ($query) {
                $query->orderBy('orderNo');
            },
            'transitions.workflowTransitionRoles',
            'transitions.workflowTransitionUsers',
            'createdByUser'
        ])->findOrFail($id);

        $stepNames = $workflow->workflowSteps->pluck('name', 'id')->toArray();

        $pendingTransitions = $workflow->transitions->map(function ($transition) use ($stepNames) {
            return [
                'id' => $transition->id,
                'name' => $transition->name,
                'fromStepId' => $transition->fromStepId,
                'fromStepName' => $stepNames[$transition->fromStepId],
                'toStepId' => $transition->toStepId,
                'toStepName' => $stepNames[$transition->toStepId],
                'assignRoles' => $transition->workflowTransitionRoles->pluck('role.name')->implode(', '),
                'assignUsers' => $transition->workflowTransitionUsers->map(function ($userTransition) {
                    return $userTransition->user->firstName . ' ' . $userTransition->user->lastName;
                })->implode(', '),
                'status' => 'InProgress',
                'comment' => ''
            ];
        });

        $nodes = $workflow->workflowSteps->map(function ($step) use ($stepNames) {
            return [
                'id' => $step->id,
                'label' => $step->name,
            ];
        });

        $links = $workflow->transitions->map(function ($transition) {
            return [
                'source' => $transition->fromStepId,
                'target' => $transition->toStepId,
                'label' => $transition->name
            ];
        });

        $customColors = $workflow->transitions->map(function ($step) {
            return [
                'name' => $step->name,
                'value' => $step->color
            ];
        });

        $visualWorkflow = [
            'workflowId' => $workflow->id,
            'workflowName' => $workflow->name,
            'workflowDescription' => $workflow->description,
            'pendingWorkflowTransitions' => $pendingTransitions,
            'nodes' => $nodes,
            'links' => $links,
            'customColors' => $customColors,
            'createdDate' => $workflow->createdDate,
            'modifiedDate' => $workflow->modifiedDate,
            'initiatedBy' => optional($workflow->createdByUser)->firstName . ' ' . optional($workflow->createdByUser)->lastName
        ];

        return response($visualWorkflow, 200);
    }

    public function getMyWorkflow()
    {
        $userId = Auth::parseToken()->getPayload()->get('userId');
        $roleIds = DB::table('userRoles')
            ->where('userId', $userId)
            ->pluck('roleId')
            ->toArray();

        $rows = DB::table('documentWorkflow as dw')
            ->select([
                'dw.*',
                'wf.name as workflowName',
                'd.name as documentName',
                'd.id as documentId',
                'ws.name as currentStepName',
                'ws_to.name as toStepName',
                // Transition fields
                'wt.id as transitionId',
                'wt.name as transitionName',
                'wt.color as transitionColor',
                'wt.toStepId as transitionToStepId',
                DB::raw("CONCAT(u.firstName,' ', u.lastName) as initiatedBy"),
                'dw.createdDate as initiatedDate'
            ])
            ->join('workflows as wf', 'wf.id', '=', 'dw.workflowId')
            ->join('documents as d', 'd.id', '=', 'dw.documentId')
            ->join('workflowSteps as ws', 'ws.id', '=', 'dw.currentStepId')
            ->join('users as u', 'u.id', 'dw.createdBy')
            ->join('workflowTransitions as wt', 'wt.fromStepId', '=', 'dw.currentStepId')
            ->leftJoin('workflowSteps as ws_to', 'ws_to.id', '=', 'wt.toStepId')
            ->where('dw.status', '!=', 'Completed')
            ->where('dw.status', '!=', 'Cancelled')
            ->where(function ($query) use ($userId, $roleIds) {
                $query->whereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                        ->from('workflowTransitionUsers')
                        ->whereRaw('workflowTransitionUsers.transitionId = wt.id')
                        ->where('workflowTransitionUsers.userId', '=', $userId);
                })->orWhereExists(function ($query) use ($roleIds) {
                    $query->select(DB::raw(1))
                        ->from('workflowTransitionRoles')
                        ->whereRaw('workflowTransitionRoles.transitionId = wt.id')
                        ->whereIn('workflowTransitionRoles.roleId', $roleIds);
                });
            })
            ->orderBy('dw.modifiedDate', 'desc')
            ->get();

        $workflows = $rows->groupBy('id')->map(function ($group) {
            $first = $group->first();

            return [
                'documentId' => $first->documentId,
                'documentWorkflowId' => $first->id,
                'workflowId' => $first->workflowId,
                'workflowName' => $first->workflowName,
                'documentName' => $first->documentName,
                'currentStepName' => $first->currentStepName,
                'initiatedBy' => $first->initiatedBy,
                'createdDate' => $first->createdDate,
                'status' => $first->status,
                'nextTransitions' => $group->map(function ($item) {
                    return [
                        'id' => $item->transitionId,
                        'name' => $item->transitionName,
                        'color' => $item->transitionColor,
                        'toStepId' => $item->transitionToStepId,
                        'fromToStepName' => $item->currentStepName . ' → ' . $item->toStepName,
                    ];
                })->unique('id')->values()
            ];
        })->values();

        return $workflows;
    }
}
