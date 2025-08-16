<?php
namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;

class AllowFileExtensions extends Model
{
    use HasFactory;
    use Notifiable, Uuids;

    public $timestamps = false;
    protected $table = 'allowFileExtensions';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id', 'fileType', 'extensions'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
