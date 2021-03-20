<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by_user');
            $table->foreign('created_by_user')->references('id')->on('users');
            $table->string('created_by_ip');
            $table->string('read_permission');
            $table->string('write_permission');
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
        Schema::dropIfExists('galleries');
    }
}
