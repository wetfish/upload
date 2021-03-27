<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreFileRequest;
use App\Models\Challenge;
use App\Models\User;
use App\Models\File;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFileRequest $request)
    {
        $input = $request->validated();
        $challenge = Challenge::where('string', $input['challenge'])->first();

        // Try to determine the file extension based on the MIME type
        $extension = $input['file']->guessExtension();
        $filename = File::uniqueName($extension);
        $input['file']->storePubliclyAs('uploads', $filename);

        // By now the file is stored on disk, so let's save it to the database!
        $file = new File;
        $file->mime_type = $input['file']->getMimeType();
        $file->uploaded_by_key = $challenge->key->id;
        $file->uploaded_by_ip = $request->ip();
        $file->system_path = "app/storage/uploads/" . $filename;
        $file->url_path = '/' . $filename;
        $file->original_file_name = $input['file']->getClientOriginalName();
        $file->hash = hash_file("sha3-256", $input['file']->getPathname());
        $file->read_permission = 'public';

        // Check if the challenge key belongs to a specific user
        if($challenge->key->user_id) {
            $file->uploaded_by_user = $challenge->key->user_id;
        }

        $file->save();

        return response()->json(['file_url' => env('FILE_URL') . $file->url_path], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
