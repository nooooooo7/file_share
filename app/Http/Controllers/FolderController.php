<?php

namespace App\Http\Controllers;

use App\Http\Requests\createFolderFormRequest;
use App\Http\Resources\folderResource;
use Illuminate\Http\Request;
use App\Models\Folder;
use Illuminate\Routing\Controller;

class FolderController extends Controller
{




    /*
You can use ordering by id or by created_at or by updated_at,
ascending(ASC) and descending(DESC) and can set how many you want
in a page by setting a value to the per_page.
*/

    public function index(Request $request)
    {
        $user = auth('api')->user();


        $folders = Folder::with('files', 'user')->withCount('files')
            ->where('user_id', $user->id)
            ->orderBy($request->by ?? 'created_at', $request->order ?? "DESC")
            ->paginate($request->per_page);

        return response()->json(folderResource::collection($folders), 200);
    }

    public function getFolder($id, Request $request)
    {
        $user = auth('api')->user();

        $folder = Folder::with('files', 'user')
            ->where('user_id', $user->id)
            ->where("id", $id)->first();

        if (!$folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }
        return new folderResource($folder);
    }


    public function createFolder(createFolderFormRequest $request)
    {
        $data = $request->validated();
        $user = auth('api')->user();


        $folder = Folder::create([
            'name' => $request->name,
            'user_id' => $user->id,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'folder created', 'data' => $folder], 201);
    }


    public function edit($id, Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            "name" => 'sometimes|string|max:35',
            "description" => 'sometimes|string|max:35'
        ]);

        $folder = Folder::where('id', $id)->where('user_id', $user->id)->first();
        if (!$folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        $folder->update($data);
        return response()->json(['message' => 'folder edited'], 201);
    }

    public function delete($id)
    {
        $user = auth('api')->user()->id;
        $folder = Folder::find($id);
        // dd($folder);
        if (!$folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }
        if ($folder->user_id !== $user) {
            return response()->json(['message' => 'user Ids do not match'], 403);
        }

        $folder->delete();
        return response()->json(['message' => 'folder deleted'], 20);
    }
}
