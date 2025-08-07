<?php

namespace App\Repositories\Implementation;

use App\Models\Clients;
use App\Models\DocumentStatus;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\DocumentStatusRepositoryInterface;
use Illuminate\Support\Facades\DB;

//use Your Model

/**
 * Class ActionsRepository.
 */
class DocumentStatusRepository extends BaseRepository implements DocumentStatusRepositoryInterface
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
        return DocumentStatus::class;
    }

    public function updateStatus($request, $id)
    {
        try {
            DB::beginTransaction();
            $model = $this->model->find($id);
            $model->fill($request);
            $model->save();
            $this->resetModel();
            $result = $this->parseResult($model);
            DB::commit();
            return response()->json($result, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error in saving data.',
            ], 409);
        }
    }
}
