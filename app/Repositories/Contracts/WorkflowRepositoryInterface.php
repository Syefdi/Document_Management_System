<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface WorkflowRepositoryInterface extends BaseRepositoryInterface
{
    public function getWorkflows();
    public function getWorkflow($id);
    public function createWorkflow($data);
    public function updateWorkflow($id, $data);
    public function deleteWorkflow($id);
    public function visualWorkflow($id);
    public function getMyWorkflow();
}
