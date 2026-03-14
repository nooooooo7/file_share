<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function uploadFile($request, $user_id)
    {
        $data = $request->validated();
        $name = Str::beforeLast($request->file('file')->getClientOriginalName(), '.');
        $format = $request->file('file')->getClientOriginalExtension();
        $size = $request->file('file')->getSize();
        $path = Storage::disk('local')->putFileAs('files', $request->file('file'), Str::slug($name).'-'.Str::random(3).'.'.$format);

        return $file = File::create([
            'user_id' => $user_id,
            'folder_id' => $data['folder_id'] ?? null,
            'name' => $name,
            'format' => $format,
            'path' => $path,
            'size' => $size,
            'visibility' => $data['visibility'] ?? 'private',
        ]);
    }

    public function changeVisibility($id, $request)
    {
        $file = File::where('id', $id)->My()->first();
        if (! $file) {
            return null;
        }
        if ($file->visibility === $request->visibility) {
            return ['status' => 'same', 'data' => $file];
        }
        $file->update(['visibility' => $request->visibility]);

        return $file;
    }

    public function download($file)
    {
        if ($file->visibility === 'private' && ! auth('api')->user()) {
            return null;
        }
        $file->increment('download_count');

        return Storage::disk('local')->download($file->path, $file->name.'.'.$file->format);
    }

    public function deleteFile($id)
    {
        $file = File::where('id', $id)->My()->first();
        if (! $file) {
            return null;
        }
        Storage::disk('local')->delete($file->path);
        $file->delete();

        return $file;
    }
}
