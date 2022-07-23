<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationMitreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_mitre', function (Blueprint $table) {
            $table->unsignedBigInteger('mitre_id');
            $table->unsignedBigInteger('application_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('mitre_id')->references('id')->on('mitres')->onDelete('cascade');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['mitre_id','application_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_mitre');
    }
}
