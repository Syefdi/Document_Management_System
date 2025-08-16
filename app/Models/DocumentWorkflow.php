<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use User;

class DocumentWorkflow extends Model
{
    use HasFactory, SoftDeletes;
    use Notifiable, Uuids;
    const CREATED_AT = 'createdDate';
    const UPDATED_AT = 'modifiedDate';

    protected $table = 'documentWorkflow'; // Table name
    protected $fillable = ['documentId', 'workflowId', 'currentStepId', 'status'];


    protected $casts = [
        'createdDate' => 'datetime',
        'modifiedDate' => 'datetime',
    ];

    /**
     * Get the document this workflow is associated with.
     */
    public function document()
    {
        return $this->belongsTo(Documents::class, 'documentId');
    }

    /**
     * Get the workflow this document is associated with.
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflowId');
    }

    public function createdByUser()
    {
        return $this->belongsTo(Users::class, 'createdBy');
    }

    /**
     * Get the current state of the workflow.
     */
    public function currentStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'currentStepId');
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

        static::addGlobalScope('documentWorkflow.isDeleted', function (Builder $builder) {
            $builder->where('documentWorkflow.isDeleted', '=', 0);
        });
    }
}
