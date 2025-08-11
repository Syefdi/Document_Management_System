<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuids;

class OpenAIDocuments extends Model
{
    use HasFactory;
    use Notifiable, Uuids;

    protected $primaryKey = "id";
    public $table = 'openaiDocuments';
    
    protected $fillable = [
        'id',
        'prompt',
        'model',
        'response',
        'language',
        'maximumLength',
        'creativity',
        'toneOfVoice',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
