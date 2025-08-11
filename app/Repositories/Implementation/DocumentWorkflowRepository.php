<?php

namespace App\Repositories\Implementation;

use App\Models\Documents;
use App\Models\DocumentWorkflow;
use App\Models\Workflow;
use App\Models\WorkflowLog;
use App\Models\WorkflowStep;
use App\Models\WorkflowTransition;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\DocumentWorkflowRepositoryInterface;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class DocumentWorkflowRepository.
 */
class DocumentWorkflowRepository extends BaseRepository implements DocumentWorkflowRepositoryInterface
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
        return DocumentWorkflow::class;
    }

    public function saveDocumentWorkflow($request)
    {
        try {

            DB::beginTransaction();

            $firstTransaction = WorkflowTransition::where('workflowId', $request->workflowId)
                ->where('isFirstTransaction', 1)
                ->first();

            if (!$firstTransaction) {
                return response()->json(['error' => 'Workflow first Transaction is not configured properly.'], 404);
            }

            $document = Documents::where('id', $request->documentId)->first();

            if (!$document) {
                return response()->json(['error' => 'Invalid document ID.'], 422);
            }

            $data = $request->all();
            $data['currentStepId'] = $firstTransaction->fromStepId;

            $model = $this->model->newInstance($data);
            $model->save();
            $this->resetModel();
            $result = $this->parseResult($model);

            $document['documentWorkflowId'] = $result->id;

            $document->save();

            $workflowLog =  WorkflowLog::create([
                'documentWorkflowId' => $result->id,
                'type' => 'Initiated',
            ]);

            $workflowLog->save();
            DB::commit();

            return $result;
        } catch (\Throwable $th) {
            return response()->json(['Message' => 'Error in saving data.' . $th->getMessage()], 409);
        }
    }

    public function visualWorkflow($id)
    {
        $documentWorkflow = $this->model->with('createdByUser')->findOrFail($id);

        $workflow = Workflow::with([
            'workflowSteps',
            'transitions' => function ($query) {
                $query->orderBy('orderNo');
            },
            'transitions.workflowTransitionRoles',
            'transitions.workflowTransitionUsers',
            'createdByUser'
        ])->findOrFail($documentWorkflow->workflowId);

        $stepNames = $workflow->workflowSteps->pluck('name', 'id')->toArray();

        $completedTransiations = WorkflowLog::with('transition', 'createdByUser')
            ->orderBy('createdDate', 'desc')
            ->where('documentWorkflowId', $id)
            ->whereNotNull('transitionId')
            ->get();

        $completedTransiationIds = $completedTransiations->pluck('transitionId')->toArray();

        $pendingTransitions = $workflow->transitions
            ->filter(function ($transition) use ($completedTransiationIds) {
                return !in_array($transition->id, $completedTransiationIds);
            })
            ->map(function ($transition) use ($stepNames) {
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
            })
            ->values();

        $completedTransiations = $completedTransiations
            ->map(function ($log) use ($stepNames) {
                return [
                    'id' => $log->id,
                    'transitionId' => $log->transitionId,
                    'name' => optional($log->transition)->name,
                    'fromStepName' => $log->transition ? $stepNames[$log->transition->fromStepId] ?? '' : '',
                    'toStepName' => $log->transition ? $stepNames[$log->transition->toStepId] ?? '' : '',
                    'comment' => $log->comment,
                    'createdBy' => optional($log->createdByUser)->firstName . ' ' . optional($log->createdByUser)->lastName,
                    'createdDate' => $log->createdDate
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
            'pendingWorkflowTransitions' => $documentWorkflow->status == 'Cancelled' ?  [] : $pendingTransitions,
            'completedWorkflowTransitions' => $completedTransiations,
            'nodes' => $nodes,
            'links' => $links,
            'customColors' => $customColors,
            'createdDate' => $workflow->createdDate,
            'initiatedDate' => $documentWorkflow->createdDate,
            'createdBy' => optional($workflow->createdByUser)->firstName . ' ' . optional($workflow->createdByUser)->lastName,
            'initiatedBy' => optional($documentWorkflow->createdByUser)->firstName . ' ' . optional($documentWorkflow->createdByUser)->lastName
        ];

        return response($visualWorkflow, 200);
    }

    public function performNextTransition($request)
    {

        try {
            DB::beginTransaction();

            $documentWorkflow = DocumentWorkflow::where('id', $request->documentWorkflowId)
                ->where('documentId', $request->documentId)
                ->first();

            if (!$documentWorkflow) {
                return response()->json(['Message' => 'Document workflow not found.'], 404);
            }

            $transition = WorkflowTransition::find($request->transitionId);

            if (!$transition) {
                return response()->json(['Message' => 'Transition not found.'], 404);
            }

            $currentStep = WorkflowStep::find($documentWorkflow->currentStepId);

            if (!$currentStep || $currentStep->id !== $transition->fromStepId) {
                return response()->json(['Message' => 'Invalid transition for the current step.'], 422);
            }

            // Update the document workflow with the new step
            $documentWorkflow->currentStepId = $transition->toStepId;

            $nextTransation = WorkflowTransition::where('workflowId', $documentWorkflow->workflowId)
                ->where('fromStepId', $transition->toStepId)
                ->first();

            $documentWorkflow->status = $nextTransation ?  'InProgress' : 'Completed'; // Assuming status is updated based on the transition
            $documentWorkflow->save();

            // Log the transition
            WorkflowLog::create([
                'documentWorkflowId' => $documentWorkflow->id,
                'transitionId' => $transition->id,
                'comment' => $request->comment ?? ''
            ]);

            DB::commit();

            return response()->json(['message' => 'Transition performed successfully.'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['Message' => 'Error in performing transition.' . $th->getMessage()], 409);
        }
    }

    public function cancelWorkflow($request, $id)
    {
        try {
            DB::beginTransaction();

            $documentWorkflow = DocumentWorkflow::findOrFail($id);

            if (!$documentWorkflow) {
                return response()->json(['Message' => 'Document workflow not found.'], 404);
            }

            $documentWorkflow->status = 'Cancelled';
            $documentWorkflow->save();

            // Log the transition
            WorkflowLog::create([
                'documentWorkflowId' => $id,
                'comment' => $request->comment ?? '',
                'type' => 'Cancelled',
            ]);

            DB::commit();
            return response()->json([], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['Message' => 'Error in performing transition.' . $th->getMessage()], 409);
        }
    }


    public function getDocumentWorkflows($attributes)
    {
        $query = DocumentWorkflow::select([
            'documentWorkflow.id',
            'documents.name as documentName',
            'workflows.name as workflowName',
            'documentWorkflow.status',
            'documentWorkflow.createdDate',
            'documentWorkflow.createdBy',
            'documentWorkflow.modifiedDate',
            'documentWorkflow.documentId',
            'documentWorkflow.currentStepId',
            DB::raw("CONCAT(modifiedUser.firstName,' ', modifiedUser.lastName) as modifiedUserName"),
            DB::raw("CONCAT(users.firstName,' ', users.lastName) as createdByName"),

            // Last transition
            'last_transition.transitionId as lastTransitionId',
            'last_transition.transitionName as lastTransitionName',
            'last_transition.fromStepId as lastFromStepId',
            'last_transition.fromStepName as lastFromStepName',
            'last_transition.toStepId as lastToStepId',
            'last_transition.toStepName as lastToStepName',
            'last_transition.performedAt as lastPerformedAt',

            // Next transitions
            'next_wt.id as nextTransitionId',
            'next_wt.name as nextTransitionName',
            'next_wt.color as nextTransitionColor',
            'next_wt.toStepId as nextToStepId',
            'next_wt.fromStepId as nextFromStepId',
            'next_fromStep.name as nextFromStepName',
            'next_toStep.name as nextToStepName'
        ])
            ->join('documents', 'documents.id', '=', 'documentWorkflow.documentId')
            ->join('users', 'documentWorkflow.createdBy', '=', 'users.id')
            ->leftJoin('users as modifiedUser', 'documentWorkflow.modifiedBy', '=', 'modifiedUser.id')
            ->leftJoin('workflows', 'documentWorkflow.workflowId', '=', 'workflows.id')

            // Last performed transition (latest by createdDate)
            ->leftJoin(DB::raw('
            (
                SELECT
                    wl.documentWorkflowId,
                    wl.transitionId,
                    wt.name as transitionName,
                    wt.fromStepId,
                    fromStep.name as fromStepName,
                    wt.toStepId,
                    toStep.name as toStepName,
                    wl.createdDate as performedAt
                FROM workflowLogs wl
                LEFT JOIN workflowTransitions wt ON wl.transitionId = wt.id
                LEFT JOIN workflowSteps fromStep ON wt.fromStepId = fromStep.id
                LEFT JOIN workflowSteps toStep ON wt.toStepId = toStep.id
                WHERE wl.transitionId IS NOT NULL
                  AND wl.createdDate = (
                      SELECT MAX(createdDate)
                      FROM workflowLogs
                      WHERE documentWorkflowId = wl.documentWorkflowId
                        AND transitionId IS NOT NULL
                      LIMIT 1
                  )
            ) as last_transition
        '), 'last_transition.documentWorkflowId', '=', 'documentWorkflow.id')

            // Next transitions based on currentStepId
            ->leftJoin('workflowTransitions as next_wt', 'next_wt.fromStepId', '=', 'documentWorkflow.currentStepId')
            ->leftJoin('workflowSteps as next_fromStep', 'next_fromStep.id', '=', 'next_wt.fromStepId')
            ->leftJoin('workflowSteps as next_toStep', 'next_toStep.id', '=', 'next_wt.toStepId');

        // Ordering
        $orderByArray = explode(' ', $attributes->orderBy);
        $orderBy = $orderByArray[0];
        $direction = $orderByArray[1] ?? 'asc';

        if ($orderBy == 'workflowName') {
            $query = $query->orderBy('workflows.name', $direction);
        } else if ($orderBy == 'createdDate') {
            $query = $query->orderBy('documentWorkflow.createdDate', $direction);
        } else if ($orderBy == 'status') {
            $query = $query->orderBy('documentWorkflow.status', $direction);
        } else if ($orderBy == 'createdByName') {
            $query = $query->orderBy('users.firstName', $direction);
        } else if ($orderBy == 'documentName') {
            $query = $query->orderBy('documents.name', $direction);
        } else if ($orderBy == 'modifiedDate') {
            $query = $query->orderBy('documentWorkflow.modifiedDate', $direction);
        }

        // Filters
        if ($attributes->workflowName) {
            $query = $query->where('workflows.name', 'like', '%' . $attributes->workflowName . '%');
        }

        if ($attributes->status) {
            $query = $query->where('documentWorkflow.status', $attributes->status);
        }

        if ($attributes->documentName) {
            $query = $query->where('documents.name', 'like', '%' . $attributes->documentName . '%');
        }

        // Pagination
        $rawResults = $query->skip($attributes->skip)->take($attributes->pageSize)->get();

        // Group results and format nextTransitions
        $results = $rawResults->groupBy('id')->map(function ($group) {
            $first = $group->first();
            return [
                'id' => $first->id,
                'documentName' => $first->documentName,
                'workflowName' => $first->workflowName,
                'status' => $first->status,
                'createdDate' => $first->createdDate,
                'createdByName' => $first->createdByName,
                'modifiedDate' => $first->modifiedDate,
                'modifiedUserName' => $first->modifiedUserName,
                'documentId' => $first->documentId,
                'lastTransition' => $first->lastTransitionId ? [
                    'id' => $first->lastTransitionId,
                    'name' => $first->lastTransitionName,
                    'fromStepId' => $first->lastFromStepId,
                    'fromStepName' => $first->lastFromStepName,
                    'toStepId' => $first->lastToStepId,
                    'toStepName' => $first->lastToStepName,
                    'performedAt' => $first->lastPerformedAt,
                ] : null,

                'nextTransitions' => $group->map(function ($item) {
                    return $item->nextTransitionId ? [
                        'id' => $item->nextTransitionId,
                        'name' => $item->nextTransitionName,
                        'color' => $item->nextTransitionColor,
                        'toStepId' => $item->nextToStepId,
                        'fromStepId' => $item->nextFromStepId,
                        'fromStepName' => $item->nextFromStepName,
                        'toStepName' => $item->nextToStepName,
                    ] : null;
                })->filter()->unique('id')->values()
            ];
        })->values();


        $results = $results->map(function ($item) {
            $item['createdDate'] = optional($item['createdDate'])->setTimezone('UTC')->toIso8601String();
            $item['modifiedDate'] = optional($item['modifiedDate'])->setTimezone('UTC')->toIso8601String();
            return $item;
        });
        return $results;
    }

    public function getDocumentWorkflowCount($attributes)
    {
        $query = DocumentWorkflow::query()
            ->join('documents', 'documents.id', '=', 'documentWorkflow.documentId')
            ->join('users', 'documentWorkflow.createdBy', '=', 'users.id')
            ->leftJoin('workflows', 'documentWorkflow.workflowId', '=', 'workflows.id');

        if ($attributes->workflowName) {
            $query = $query->where('workflows.name', 'like', '%' . $attributes->workflowName . '%');
        }

        if ($attributes->status) {
            $query = $query->where('documentWorkflow.status', $attributes->status);
        }

        if ($attributes->documentName) {
            $query = $query->where('documents.name', 'like', '%' . $attributes->documentName . '%');
        }

        $count = $query->count();
        return $count;
    }
}
