<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogDataRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_data_rule', function (Blueprint $table) {
            $table->unsignedBigInteger('log_data_id');
            $table->unsignedBigInteger('rule_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('log_data_id')->references('id')->on('log_data')->onDelete('cascade');
            $table->foreign('rule_id')->references('id')->on('rules')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['log_data_id','rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_data_rule');
    }
}
