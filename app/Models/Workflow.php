<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;
    use Notifiable, Uuids;
    protected $primaryKey = "id";

    const CREATED_AT = 'createdDate';
    const UPDATED_AT = 'modifiedDate';

    protected $table = 'workflows';
    protected $fillable = ['name', 'description', 'isWorkflowSetup'];


    /**
     * Get the states for the workflow.`
     */
    public function workflowSteps()
    {
        return $this->hasMany(WorkflowStep::class, 'workflowId');
    }

    /**
     * Get the transitions for the workflow.
     */
    public function transitions()
    {
        return $this->hasMany(WorkflowTransition::class, 'workflowId');
    }

    public function createdByUser()
    {
        return $this->belongsTo(Users::class, 'createdBy');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $userId = Auth::parseToken()->getPayload()->get('userId');
            $model->createdBy = $userId;
            $model->modifiedBy = $userId;
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });

        static::updating(function (Model $model) {
            $userId = Auth::parseToken()->getPayload()->get('userId');
            $model->modifiedBy = $userId;
        });

        static::addGlobalScope('workflows.isDeleted', function (Builder $builder) {
            $builder->where('workflows.isDeleted', '=', 0);
        });
    }
}
