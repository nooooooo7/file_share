<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileMetaDataFormRequest;
use App\Http\Requests\UploadFileFormRequest;
use App\Http\Requests\VisibilityFormRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\FolderResource;
use App\Models\File;
use App\Models\Folder;
use App\Services\FileService;
use Illuminate\Routing\Controller;

class FileController extends Controller
{
    public $fileService;

    public function __construct()
    {
        $this->fileService = new FileService;
    }

    public function index(FileMetaDataFormRequest $request)
    {
        $files = File::searchByName($request->search ?? '')
            ->orderBy($request->by ?? 'created_at', $request->order ?? 'DESC')
            ->paginate($request->per_page);

        return FileResource::collection($files);
    }

    public function show($id)
    {
        $result = File::where('id', $id)
            ->My()->first();
        if (! $result) {
            return response()->json(['message' => 'file not found'], 404);
        }

        return new FileResource($result);
    }

    public function addToFolder($id, $folder_id)
    {
        $file = File::where('id', $id)->My()->first();
        $folder = Folder::with('files')->where('id', $folder_id)->My()->first();

        if (! $file) {
            return response()->json(['message' => 'file not found'], 404);
        }
        if (! $folder) {
            return response()->json(['message' => 'folder not found'], 404);
        }

        if ($file->folder_id === $folder->id) {
            return response()->json(['message' => 'file already exists in this folder'], 409);
        }
        $file->update(['folder_id' => $folder_id]);

        return response()->json(['data' => new FolderResource($folder)], 200);
    }

    public function changeVisibility($id, VisibilityFormRequest $request)
    {
        $file = $this->fileService->changeVisibility($id, $request);
        if (! $file) {
            return response()->json(['message' => 'file not found'], 404);
        }
        if ($file['status'] === 'same') {
            return response()->json(['message' => "visibility is already set to {$request->visibility}.", 'data' => new FileResource($file['data'])], 409);
        }

        return response()->json(['message' => "visibility has been changed to {$file->visibility}.", 'data' => new FileResource($file)], 200);
    }

    public function removeFromFolder($id)
    {
        $file = File::where('id', $id)->My()->first();
        if (! $file) {
            return response()->json(['message' => 'file not found'], 404);
        }
        if (! $file->folder_id) {
            return response()->json(['message' => 'file is not in a folder'], 409);
        }
        $file->update(['folder_id' => null]);

        return response()->json(['message' => 'file removed from folder', 'data' => new FileResource($file)], 200);
    }

    public function upload(UploadFileFormRequest $request)
    {
        $user_id = auth('api')->user()->id;
        $file = $this->fileService->uploadFile($request, $user_id);

        return response()->json([
            'message' => 'file uploaded!',
            'data' => new FileResource($file),
        ], 201);
    }

    public function generateLink($file_id)
    {
        $file = File::where('id', $file_id)->My()->first();
        if (! $file) {
            return response()->json(['message' => 'could not find this file'], 404);
        }

        return response()->json(['link' => url("/api/file/download/{$file_id}")], 200);
    }

    public function download(File $file)
    {
        $result = $this->fileService->download($file);
        if (! $result) {
            return response()->json(['message' => 'unauthorized'], 401);
        }

        return $result;
    }

    public function destroy($id)
    {
        $file = $this->fileService->deleteFile($id);
        if (! $file) {
            return response()->json(['message' => 'could not find this file'], 404);
        }

        return response()->json(['message' => "file {$file->name} has been deleted"], 200);
    }
}
