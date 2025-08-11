<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface WorkflowTransitionRepositoryInterface extends BaseRepositoryInterface
{
    public function getWorkflowTransitions($id);
    public function createWorkflowTransition($request);
    public function updateWorkflowTransition($id, $data);
}
