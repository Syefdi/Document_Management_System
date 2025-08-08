<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface DocumentWorkflowRepositoryInterface extends BaseRepositoryInterface
{
    public function saveDocumentWorkflow($request);
    public function performNextTransition($request);
    public function cancelWorkflow($request, $id);
    public function visualWorkflow($id);
    public function getDocumentWorkflows($request);
    public function getDocumentWorkflowCount($request);
}
