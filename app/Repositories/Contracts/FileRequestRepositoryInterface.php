<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface FileRequestRepositoryInterface extends BaseRepositoryInterface
{
    public function createFileRequest($attributes);
    public function updateFileRequest($attribute, $id);
    public function getFileRequest($attribute);
    public function getFileRequestCount($attribute);
    public function varifyPassword($password, $id);
    public function getFileRequestData($id);
}
