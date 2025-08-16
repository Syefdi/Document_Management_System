<?php

namespace App\Repositories\Contracts;


interface PageHelperRepositoryInterface extends BaseRepositoryInterface
{
    public function getByCode($code);
}
