<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('tag_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['content_id','tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_tag');
    }
}
