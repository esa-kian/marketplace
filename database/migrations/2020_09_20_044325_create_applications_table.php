<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('overview')->nullable();
            $table->text('details')->nullable();
            $table->string('release_notes')->nullable();
            $table->string('contents')->nullable();
            $table->integer('number_of_rules')->nullable();
            $table->integer('number_of_dashboards')->nullable();
            $table->integer('number_of_alerts')->nullable();

            //Start Supported Platforms
            $table->boolean('splunk')->nullable();
            $table->boolean('arcsight')->nullable();
            $table->boolean('qradar')->nullable();
            $table->boolean('other')->nullable();
            //End Supported Platforms

            $table->string('data_sources')->nullable();
            $table->string('requirements')->nullable();

            // Start Kill chain Phases
            $table->boolean('reconnaissance')->nullable();
            $table->boolean('weaponization')->nullable();
            $table->boolean('delivery')->nullable();
            $table->boolean('exploitation')->nullable();
            $table->boolean('installation')->nullable();
            $table->boolean('command_and_control')->nullable();
            $table->boolean('actions_on_objective')->nullable();
            // End Kill chain Phases

            $table->string('version')->nullable();
            $table->string('built_by')->nullable();
            $table->string('compatibility')->nullable();
            $table->string('licensing')->nullable();
            $table->string('picture')->nullable();
            $table->string('attachment')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('applications');
    }
}
