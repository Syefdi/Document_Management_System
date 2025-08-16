<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuids;

class PageHelper extends Model
{
    use HasFactory;
    use Notifiable, Uuids;
    protected $primaryKey = "id";
    public $timestamps = false;
    public $table = 'pageHelper';

    protected $fillable = [
        'name',
        'code',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
