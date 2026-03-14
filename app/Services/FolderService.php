<?php

namespace App\Services;

use App\Models\Folder;

class FolderService
{
    public function createFolder($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->user()->id;
        return Folder::create($data);
    }

    public function editFolder($id, $request)
    {
        $data = $request->validated();
        $folder = Folder::where('id', $id)->My()->first();
        if (! $folder) {
            return null;
        }
        $folder->update($data);

        return $folder;
    }

    public function delete($id)
    {
        $folder = Folder::where('id', $id)->My()->first();
        if (! $folder) {
            return null;
        }

        $folder->delete();

        return $folder;
    }
}
