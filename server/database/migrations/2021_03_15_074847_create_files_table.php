<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('mime_type');
            $table->foreignId('gallery_id')->nullable()->constrained();
            $table->unsignedBigInteger('uploaded_by_user');
            $table->foreign('uploaded_by_user')->references('id')->on('users');
            $table->string('uploaded_by_ip');
            $table->string('file_path', 2048);
            $table->string('url_path', 2048);
            $table->string('original_file_name', 2048);
            $table->string('hash');
            $table->string('read_permission');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
