<?php

namespace App\Repositories\Implementation;

use App\Models\AllowFileExtensions;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\AllowFileExtensionRepositoryInterface;

//use Your Model

/**
 * Class ActionsRepository.
 */
class AllowFileExtensionRepository extends BaseRepository implements AllowFileExtensionRepositoryInterface
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
        return AllowFileExtensions::class;
    }
}