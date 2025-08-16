<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class WorkflowTransitionRole extends Model
{
    use HasFactory;

    protected $table = 'workflowTransitionRoles'; // Table name
    public $timestamps = false;
    protected $fillable = ['roleId', 'transitionId'];

    /**
     * Get the user assigned to this transition.
     */
    public function role()
    {
        return $this->belongsTo(Roles::class, 'roleId');
    }

    /**
     * Get the transition this assignment belongs to.
     */
    public function workflowTransition()
    {
        return $this->belongsTo(WorkflowTransition::class, 'transitionId');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
