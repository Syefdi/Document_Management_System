<?php

namespace App\Repositories\Implementation;

use App\Models\PageHelper;
use App\Repositories\Contracts\PageHelperRepositoryInterface;
use App\Repositories\Implementation\BaseRepository;


//use Your Model

/**
 * Class PageHelperRepository.
 */
class PageHelperRepository extends BaseRepository implements PageHelperRepositoryInterface
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
        return PageHelper::class;
    }

    public function getByCode($code)
    {
        return PageHelper::where('code', $code)->first();
    }
}
