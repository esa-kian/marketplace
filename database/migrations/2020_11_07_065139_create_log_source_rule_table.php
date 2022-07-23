<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogSourceRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_source_rule', function (Blueprint $table) {
            $table->unsignedBigInteger('log_source_id');
            $table->unsignedBigInteger('rule_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('log_source_id')->references('id')->on('log_sources')->onDelete('cascade');
            $table->foreign('rule_id')->references('id')->on('rules')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['log_source_id','rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_source_rule');
    }
}
