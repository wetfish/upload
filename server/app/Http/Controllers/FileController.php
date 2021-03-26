<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreFileRequest;

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

        // Check if the challenge key belongs to a specific user

        // Check the mime type

        // Hash the file

/*
$table->id();
$table->string('title')->nullable();
$table->text('description')->nullable();
$table->string('mime_type');
$table->foreignId('gallery_id')->nullable()->constrained();
$table->unsignedBigInteger('uploaded_by_key');
$table->foreign('uploaded_by_key')->references('id')->on('keys');
$table->unsignedBigInteger('uploaded_by_user');
$table->foreign('uploaded_by_user')->references('id')->on('users');
$table->string('uploaded_by_ip');
$table->string('file_path', 2048);
$table->string('url_path', 2048);
$table->string('original_file_name', 2048);
$table->string('hash');
$table->string('read_permission');
$table->timestamps();
*/

      print_r($input);

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
