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

class AIPromptTemplates extends Model
{
    use HasFactory, SoftDeletes;
    use Notifiable, Uuids;

    protected $primaryKey = "id";
    public $table = 'aiPromptTemplates';
    const UPDATED_AT = 'modifiedDate';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'promptInput',
        'modifiedDate'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
