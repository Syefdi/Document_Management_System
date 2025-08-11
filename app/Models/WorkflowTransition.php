<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class WorkflowTransition extends Model
{
    use HasFactory;

    protected $table = 'workflowTransitions'; // Table name
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";
    protected $fillable = ['workflowId', 'fromStepId', 'toStepId', 'name', 'color', 'isFirstTransaction', 'orderNo'];

    protected $casts = [
        'isFirstTransaction' => 'boolean'
    ];

    /**
     * Get the workflow this transition belongs to.
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflowId');
    }

    /**
     * Get the source Step of the transition.
     */
    public function fromWorkflowStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'fromStepId');
    }

    /**
     * Get the destination Step of the transition.
     */
    public function toWorkflowStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'toStepId');
    }


    public function workflowTransitionRoles()
    {
        return $this->hasMany(WorkflowTransitionRole::class, 'transitionId');
    }


    public function workflowTransitionUsers()
    {
        return $this->hasMany(WorkflowTransitionUser::class, 'transitionId');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
