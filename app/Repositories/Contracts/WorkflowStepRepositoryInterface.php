<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface WorkflowStepRepositoryInterface extends BaseRepositoryInterface
{
    public function getWorkflowSteps($id);
    public function updateWorkflowStep($id,$request);
    public function createWorkflowStep($data);
}
