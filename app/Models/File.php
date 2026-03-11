<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{

    use SoftDeletes, Searchable;

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'format',
        'path',
        'size',
        'download_count',
        'visibility'
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

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
