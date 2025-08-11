<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Builder;

class Documents extends Model
{
    use HasFactory, SoftDeletes;
    use Notifiable, Uuids;

    const CREATED_AT = 'createdDate';
    const UPDATED_AT = 'modifiedDate';
    protected $table = 'documents';
    public $incrementing = false;

    protected $fillable = [
        'categoryId',
        'name',
        'description',
        'url',
        'createdBy',
        'modifiedBy',
        'isIndexed',
        'isDeleted',
        'location',
        'isPermanentDelete',
        'clientId',
        'statusId',
        'documentWorkflowId',
        'currentWorkflowStepId'
    ];

    protected $casts = [
        'isIndexed' => 'boolean',
        'isDeleted' => 'boolean',
        'isPermanentDelete' => 'boolean',
        'isWorkflowCompleted' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsTo(Categories::class, 'categoryId', 'id');
    }

    public function clients()
    {
        return $this->belongsTo(Clients::class, 'clientId', 'id');
    }

    public function docuementStatus()
    {
        return $this->belongsTo(DocumentStatus::class, 'statusId', 'id');
    }

    public function users()
    {
        return $this->belongsTo(Users::class, 'createdBy', 'id');
    }

    public function documentMetaDatas()
    {
        return $this->hasMany(DocumentMetaDatas::class, 'documentId');
    }

    public function documentComments()
    {
        return $this->hasMany(DocumentComments::class, 'documentId');
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotifications::class, 'documentId');
    }

    public function reminderSchedulers()
    {
        return $this->hasMany(ReminderSchedulers::class, 'documentId');
    }

    public function reminders()
    {
        return $this->hasMany(Reminders::class, 'documentId');
    }

    public function documentVersions()
    {
        return $this->hasMany(DocumentVersions::class, 'documentId');
    }

    public function documentUserPermissions()
    {
        return $this->hasMany(DocumentUserPermissions::class, 'documentId');
    }
    
    public function documentRolePermissions()
    {
        return $this->hasMany(DocumentRolePermissions::class, 'documentId');
    }

    public function documentAuditTrails()
    {
        return $this->hasMany(DocumentAuditTrails::class, 'documentId');
    }

    public function documentWorkflow()
    {
        return $this->belongsTo(DocumentWorkflow::class, 'documentWorkflowId', 'id');
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

        static::addGlobalScope('isDeleted', function (Builder $builder) {
            $builder->where('documents.isDeleted', '=', 0);
        });

        static::addGlobalScope('isPermanentDelete', function (Builder $builder) {
            $builder->where('documents.isPermanentDelete', '=', 0);
        });
    }
}
