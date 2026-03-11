<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UploadFileFormRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController
{
    public function upload(UploadFileFormRequest $request)
    {
        $user = auth('api')->user();
        $data = $request->validated();

        $name = Str::beforeLast($request->file('file')->getClientOriginalName(), '.');
        $format = $request->file('file')->getClientOriginalExtension();
        $size = $request->file('file')->getSize();

        $path = Storage::disk('s3')->putFileAs('files', $request->file('file'), Str::slug($name) . "-" . Str::random(3) . "." . $format);

        $file = File::create([
            'user_id' => $user->id,
            'folder_id' => $data['folder_id'] ?? null,
            'name' => $name,
            'format' => $format,
            'path' => $path,
            'size' => $size,
            'visibility' => $data['visibility'] ?? 'private',
        ]);


        return response()->json([
            "message" => 'file uploaded!',
            'data' => new FileResource($file)
        ], 201);
    }


    /*
order is done "by" creation date and file size.
DESC+created_at = latest
*/
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $files = File::where('user_id', $user->id)
            ->orderBy($request->by ?? 'created_at', $request->order ?? "DESC")
            ->paginate($request->per_page);

        return FileResource::collection($files);
    }

    /*
add search as a query param
*/
    public function search(Request $request)
    {
        $user = auth('api')->user();
        $result = File::search($request->search)
            ->where('user_id', $user->id)->paginate($request->per_page ?? 10);

        return FileResource::collection($result);
    }


    public function delete($id)
    {
        $user = auth('api')->user();

        $target = File::where('user_id', $user->id)->where('id', $id)->first();
        if (!$target) {
            return response()->json(['message' => "could not find this file"], 404);
        }
        Storage::disk('s3')->delete($target->path);
        $target->delete();

        return response()->json(['message' => "file {$target->name} has been deleted"], 200);
    }


    public function addToFolder($id, $folder_id)
    {
        $user = auth('api')->user();
        $folder = Folder::find($folder_id);
        if($folder->user_id !== $user->id){
return response()->json(['message'=>'unauthorized to manage this folder'],403);
        }
        $file = File::where('id', $id)->where('user_id', $user->id)->first();

        if (!$file) {
            return response()->json(['message' => "could not find this file"], 404);
        }

        $file->update(["folder_id" => $folder_id]);
        return response()->json(['message' => "file has been added to folder"], 200);
    }


    public function changeVisibility($id, Request $request)
    {
        $user = auth('api')->user();
        $file = File::where('user_id', $user->id)->where('id', $id)->first();
        if (!$file) {
            return response()->json(['message' => "could not find this file"], 404);
        }

        $file->update(["visibility" => $request->visibility]);
        return response()->json(['message' => "visibility has been changed to {$request->visibility}."], 200);
    }



    public function generateLink($file_id)
    {
        $user = auth('api')->user();

        $file = File::where('user_id', $user->id)->where('id', $file_id)->first();
        if (!$file) {
            return response()->json(['message' => "could not find this file"], 404);
        }
        return response()->json(['link' => url("/api/file/download/{$file_id}")], 200);
    }




    public function download(File $file)
    {

        if ($file->visibility === 'private') {

            if (!auth('api')->user()) {
                return response()->json(['message' => 'unauthorized'], 401);
            }
        }

        $file->increment('download_count');

        return Storage::disk('s3')->download($file->path, $file->name . '.' . $file->format);
    }
}
