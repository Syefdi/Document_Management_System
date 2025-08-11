<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface FileRequestDocumentRepositoryInterface extends BaseRepositoryInterface
{
    function saveDocument($request, $path, $fileRequestId, $name);
    function getFileRequestDocumentData($id);
    function approveDocument($request);
    function rejectDocument($request);
}
