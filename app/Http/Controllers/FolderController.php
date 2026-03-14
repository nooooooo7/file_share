<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFolderFormRequest;
use App\Http\Requests\FolderEditFormRequest;
use App\Http\Requests\FolderMetaDataFormRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FolderController extends Controller
{
    public $folderService;

    public function __construct()
    {
        $this->folderService = new FolderService;
    }

    public function index(FolderMetaDataFormRequest $request)
    {
        $folders = Folder::with('files', 'user')->withCount('files')
            ->My()
            ->orderBy($request->by ?? 'created_at', $request->order ?? 'DESC')
            ->paginate($request->per_page);

        return response()->json(FolderResource::collection($folders), 200);
    }

    public function show($id)
    {
        $folder = Folder::with('files', 'user')
            ->My()
            ->where('id', $id)->first();
        if (! $folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        return new FolderResource($folder);
    }

    public function store(CreateFolderFormRequest $request)
    {
        $folder = $this->folderService->createFolder($request);
        return response()->json(['message' => 'folder created', 'data' => new FolderResource($folder)], 201);
    }

    public function update($id, FolderEditFormRequest $request)
    {
        $folder = $this->folderService->editFolder($id, $request);
        if (! $folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        return response()->json(['message' => 'folder edited', 'data' => new FolderResource($folder)], 200);
    }

    public function destroy($id)
    {
        $folder = $this->folderService->delete($id);
        if (! $folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        return response()->json(['message' => 'folder deleted'], 200);
    }
}
