<?php

namespace App\Repositories\Implementation;

use App\Models\Clients;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\ClientRepositoryInterface;

//use Your Model

/**
 * Class ActionsRepository.
 */
class ClientRepository extends BaseRepository implements ClientRepositoryInterface
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
        return Clients::class;
    }
}