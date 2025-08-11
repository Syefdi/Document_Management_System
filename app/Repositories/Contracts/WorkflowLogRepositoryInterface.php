<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface WorkflowLogRepositoryInterface extends BaseRepositoryInterface
{
    public function getWorkflowLogs($attributes);
    public function getWorkflowLogCount($attributes);
}
