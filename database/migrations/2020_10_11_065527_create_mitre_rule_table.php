<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitreRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mitre_rule', function (Blueprint $table) {
            $table->unsignedBigInteger('mitre_id');
            $table->unsignedBigInteger('rule_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('mitre_id')->references('id')->on('mitres')->onDelete('cascade');
            $table->foreign('rule_id')->references('id')->on('rules')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['mitre_id','rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mitre_rule');
    }
}
