<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOsPlatformRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_platform_rule', function (Blueprint $table) {
            $table->unsignedBigInteger('os_platform_id');
            $table->unsignedBigInteger('rule_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('os_platform_id')->references('id')->on('os_platforms')->onDelete('cascade');
            $table->foreign('rule_id')->references('id')->on('rules')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['os_platform_id','rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_platform_rule');
    }
}
