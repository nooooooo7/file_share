<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class File extends Model
{
    use Searchable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'format',
        'path',
        'size',
        'download_count',
        'visibility',
    ];

    protected $attributes = [
        'visibility' => 'private',
        'download_count' => 0,
    ];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public static function searchByName($term)
    {
        return static::search($term)->where('user_id', auth('api')->user()->id);
    }

    public function scopeMy($query)
    {
        $user = auth('api')->user();

        return $query->where('user_id', $user->id);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
