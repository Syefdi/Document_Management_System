<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface DocumentShareableLinkRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrUpdateLink($attribute);
    public function deleteLink($id);
    public function getLink($id);
    public function getByCode($code);
    public function getLinkInfoByCode($id);
    public function getDocumentByCode($id);
    public function validatePassword($code, $password);
    public function getDocumentToken($token);
}
