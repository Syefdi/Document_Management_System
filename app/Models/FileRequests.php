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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileRequests extends Model
{
    use HasFactory, SoftDeletes;
    use Notifiable, Uuids;

    protected $table = 'fileRequests';
    protected $primaryKey = "id";
    const CREATED_AT = 'createdDate';
    const UPDATED_AT = 'modifiedDate';

    protected $fillable = [
        'id',
        'subject',
        'email',
        'password',
        'maxDocument',
        'sizeInMb',
        'allowExtension',
        'fileRequestStatus',
        'linkExpiryTime',
        'isLinkExpired'
    ];

    protected $casts = [
        'fileRequestStatus' => 'integer',
        'createdDate' => 'datetime',
        'linkExpiryTime' => 'datetime',
        'isLinkExpired' => 'boolean'
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'createdBy');
    }

    public function fileRequestDocuments(): HasMany
    {
        return $this->hasMany(FileRequestDocuments::class, 'fileRequestId');
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
            if (Auth::check()) {
                $userId = Auth::parseToken()->getPayload()->get('userId');
                $model->modifiedBy = $userId;
            }
        });
        static::addGlobalScope('isDeleted', function (Builder $builder) {
            $builder->where('fileRequests.isDeleted', '=', 0);
        });
    }
}
