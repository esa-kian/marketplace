<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuleUseCaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rule_use_case', function (Blueprint $table) {
            $table->unsignedBigInteger('use_case_id');
            $table->unsignedBigInteger('rule_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('use_case_id')->references('id')->on('use_cases')->onDelete('cascade');
            $table->foreign('rule_id')->references('id')->on('rules')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['use_case_id','rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rule_use_case');
    }
}
