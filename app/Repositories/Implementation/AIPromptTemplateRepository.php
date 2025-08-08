<?php

namespace App\Repositories\Implementation;

use App\Models\AIPromptTemplates;
use App\Models\AllowFileExtensions;
use App\Models\AllowFileTypeEnum;
use App\Models\Users;
use App\Repositories\Contracts\EmailRepositoryInterface;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\AIPromptTemplateRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AIPromptTemplateRepository extends BaseRepository implements AIPromptTemplateRepositoryInterface
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
        return AIPromptTemplates::class;
    }

}
